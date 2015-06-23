<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use AppBundle\Entity\Image;
use AppBundle\Entity\Vote;
use AppBundle\Form\Type\UploadFormType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Filesystem\Filesystem;

class GalleryController extends Controller
{

    const MAX_PER_PAGE = 8;

    public function indexAction(Request $request)
    {
        $q           = $request->query->get('q');
        $currentPage = ($request->query->get('page') < 1) ? 1 : $request->query->get('page');

        if (in_array($request->query->get('sortBy'), ['created', 'rating', 'title'], true) === false) {
                $request->query->set('sortBy', 'created');
        }

        $sortBy = $request->query->get('sortBy');

        if (in_array($request->query->get('order'), ['asc', 'desc']) === false) {
            $request->query->set('order', 'desc');
        }

        $order      = $request->query->get('order');
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Image');
        if ($q !== null) {
            $query = $repository->searchForQuery($q, $sortBy, $order);
        } else {
            $query = $repository->getImagesQuery($sortBy, $order);
        }

        $adapter            = new DoctrineORMAdapter($query);
        $pagerfanta         = new Pagerfanta($adapter);
        $currentPageResults = $pagerfanta->setMaxPerPage(self::MAX_PER_PAGE)
                                         ->setCurrentPage($currentPage)
                                         ->getCurrentPageResults();
        return $this->render(
            'AppBundle:Twig:gallery.html.twig',
            array(
             'title'   => 'sandbox|gallery',
             'content' => $currentPageResults,
             'pager'   => $pagerfanta,
            )
        );

    }//end indexAction()


    public function imageAction($id)
    {
        $user          = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $image         = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));

        if ($image === null) {
            throw $this->createNotFoundException('No image with id '.$imageId);
        }
        if ($user === null) {
            throw $this->createNotFoundException(('no user'));
        }

        $votes = $entityManager->getRepository('AppBundle:Vote')->countVotes($image);
        $test = $entityManager->getRepository('AppBundle:Vote')->checkForVote($user, $image);
        return $this->render('AppBundle:Twig:image.html.twig', array('title' => 'sandbox|image', 'image' => $image, 'test' =>$test, 'votes' =>$votes));

    }//end imageAction()


    public function uploadAction(Request $request)
    {
        $image = new Image();
        $form  = $this->createForm(new UploadFormType());
        $user  = $this->getUser();
        $form->handleRequest($request);
        if ($form->isValid() === true) {
            if ($this->get('security.authorization_checker')->isGranted('create', $image, $user) === false) {
                throw new AccessDeniedException('Unauthorised access!');
            }

            $data             = $form->getData();
            $imageSizeDetails = getimagesize($data['file']->getPathName());
            $randomFileName   = sha1(uniqid());
            $image->setFileName($randomFileName)
                  ->setSize($data['file']->getSize())
                  ->setResolution(strval($imageSizeDetails[0]).' x '.strval($imageSizeDetails[1]))
                  ->setExtension($data['file']->getClientOriginalExtension())
                  ->setTitle($data['title'])
                  ->setDescription($data['description'])
                  ->setOwner($user);
            $imageDir = $this->get('kernel')->getRootDir().'/../web/images'.$this->getRequest()->getBasePath();
            $data['file']->move($imageDir, $randomFileName.'.'.$data['file']->getClientOriginalExtension());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();
            $this->addFlash('success', 'Image uploaded!');
            return $this->redirectToRoute('_gallery');
        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('warning', 'Image upload error!');
        }//end if
        return $this->render('AppBundle:Twig:upload.html.twig', array('title' => 'sandbox|project', 'form' => $form->createView()));

    }//end uploadAction()


    public function imageEditAction(Request $request, $imageId)
    {
        $entityManager    = $this->getDoctrine()->getManager();
        $image = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $imageId));

        if ($this->get('security.authorization_checker')->isGranted('edit', $image) === false) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $defaultData = array('message' => 'Enter image description.');
        $form        = $this->createFormBuilder($defaultData)
            ->add('title', 'text', array('data' => $image->getTitle(), 'constraints' => new Length(array('min' => 3), new NotBlank)))
            ->add('description', 'textarea', array( 'data' => $image->getDescription(), 'required' => true))
            ->add('Save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid() === true) {
            $data = $form->getData();
            $image->setTitle($data['title'])->setDescription($data['description'])->setUpdated(new \Datetime());
            $entityManager->flush();
            return $this->redirectToRoute('_image', array('id' => $imageId));
        }

        return $this->render('AppBundle:Twig:image.html.twig', array('title' => 'sandbox|image', 'image' => $image, 'form' => $form->createView()));

    }//end imageEditAction()


    public function imageDeleteAction(Request $request, $imageId)
    {
        $em    = $this->getDoctrine()->getManager();
        $image = $em->getRepository('AppBundle:Image')->findOneBy(array('id' => $imageId));

        if ($this->get('security.authorization_checker')->isGranted('delete', $image) === false) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        if ($image === false) {
                throw $this->createNotFoundException('No image with id '.$imageId);
        } else {
                $em->remove($image);
                $em->flush();
                $fileSystem       = new Filesystem();
                $imageDir = $this->get('kernel')->getRootDir().'/../web/images'.$this->getRequest()->getBasePath();
                $fileSystem->remove($imageDir.$image->getFileName().'.'.$image->getExtension());
                $fileSystem->remove($imageDir.'/cache/thumb/'.$image->getFileName().'.'.$image->getExtension());
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Image deleted!');
                return $this->redirectToRoute('_gallery');
        }

    }//end imageDeleteAction()


    public function imageVoteAction(Request $request)
    {
        $voteValue     = $request->request->get('voteValue');
        $imageId       = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $image         = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $imageId));
        $user          = $this->getUser();
        $voteWhiteList = array(-1, 1);

        $voteCheck = $entityManager->getRepository('AppBundle:Vote')->findOneBy(array('user' => $user, 'image' => $image));

        if ($this->get('security.authorization_checker')->isGranted('vote', $image, $user) === false || $voteCheck !== null || in_array($voteValue, $voteWhiteList) === false) {
            throw new AccessDeniedException('Unauthorised access to voting!');
        }

        $vote = new Vote();
        $vote->setImage($image)->setUser($user)->setVote($voteValue);
        $entityManager->persist($vote);
        $entityManager->flush();
        $request->getSession()
                ->getFlashBag()
                ->add('success', 'Vote recorded, thanks!');
        return $this->redirectToRoute('_gallery');

    }//end voteAction()


}//end class
