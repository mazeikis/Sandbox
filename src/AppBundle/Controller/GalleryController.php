<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use AppBundle\Entity\Image;
use AppBundle\Form\Type\UploadFormType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Filesystem\Filesystem;

class GalleryController extends Controller
{


    public function indexAction(Request $request)
    {
        $q           = $request->query->get('q');
        $currentPage = ($request->query->get('page') < 1) ? 1 : $request->query->get('page');

        if (in_array($request->query->get('sortBy'), ['created', 'owner', 'title'], true) === false) {
                $request->query->set('sortBy', 'created');
        }

        $sortBy = $request->query->get('sortBy');

        if (in_array($request->query->get('order'), ['asc', 'desc']) === false) {
            $request->query->set('order', 'desc');
        }

        $order = $request->query->get('order');
        if ($q === true) {
            $query = $this->getDoctrine()->getManager()->getRepository('AppBundle:Image')->searchForQuery($q, $sortBy, $order);
        } else {
            $query = $this->getDoctrine()->getManager()->createQueryBuilder()
              ->select('image')
              ->from('AppBundle:Image', 'image')
              ->orderBy('image.'.$sortBy, $order);
        }

        $adapter    = new DoctrineORMAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(8)->setCurrentPage($currentPage);
        $currentPageResults = $pagerfanta->getCurrentPageResults();
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
        $em    = $this->getDoctrine()->getManager();
        $image = $em->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));
        if ($image === false) {
            throw $this->createNotFoundException('No image with id '.$id);
        }

        return $this->render('AppBundle:Twig:image.html.twig', array('title' => 'sandbox|image', 'image' => $image));

    }//end imageAction()


    public function uploadAction(Request $request)
    {
        $image = new Image();
        $form  = $this->createForm(new UploadFormType());
        $user  = $this->getUser();
        $form->handleRequest($request);
        if ($form->isValid() === ture) {
            if ($this->get('security.authorization_checker')->isGranted('create', $image, $user) === false) {
                throw new AccessDeniedException('Unauthorised access!');
            }

            $data             = $form->getData();
            $imageSizeDetails = getimagesize($data['file']->getPathName());
            $randomFileName   = sha1(uniqid(mt_rand(), true));
            $image->setFileName($randomFileName)
                  ->setSize($data['file']->getSize())
                  ->setResolution(strval($imageSizeDetails[0]).' x '.strval($imageSizeDetails[1]))
                  ->setExtension($data['file']->getClientOriginalExtension())
                  ->setTitle($data['title'])
                  ->setDescription($data['description'])
                  ->setOwner($user);
            $imageDir = $this->get('kernel')->getRootDir().'/../web/images'.$this->getRequest()->getBasePath();
            $data['file']->move($imageDir, $randomFileName.'.'.$data['file']->getClientOriginalExtension());
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Image uploaded!');
            return $this->redirectToRoute('_gallery');
        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('warning', 'Image upload error!');
        }//end if
        return $this->render('AppBundle:Twig:upload.html.twig', array('title' => 'sandbox|project', 'form' => $form->createView()));

    }//end uploadAction()


    public function imageEditAction(Request $request, $id)
    {
        $em    = $this->getDoctrine()->getManager();
        $image = $em->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));

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
            $em->flush();
            return $this->redirectToRoute('_image', array('id' => $id));
        }

        return $this->render('AppBundle:Twig:image.html.twig', array('title' => 'sandbox|image', 'image' => $image, 'form' => $form->createView()));

    }//end imageEditAction()


    public function imageDeleteAction(Request $request, $id)
    {
        $em    = $this->getDoctrine()->getManager();
        $image = $em->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));

        if ($this->get('security.authorization_checker')->isGranted('delete', $image) === false) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        if ($image === false) {
                throw $this->createNotFoundException('No image with id '.$id);
        } else {
                $em->remove($image);
                $em->flush();
                $fs       = new Filesystem();
                $imageDir = $this->get('kernel')->getRootDir().'/../web/images'.$this->getRequest()->getBasePath();
                $fs->remove($imageDir.$image->getFileName().'.'.$image->getExtension());
                $fs->remove($imageDir.'/cache/thumb/'.$image->getFileName().'.'.$image->getExtension());
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Image deleted!');
                return $this->redirectToRoute('_gallery');
        }

    }//end imageDeleteAction()


}//end class
