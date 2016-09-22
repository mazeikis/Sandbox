<?php
 
namespace AppBundle\Controller;
 
use AppBundle\Event\ImageEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use AppBundle\Entity\Image;
use AppBundle\Entity\Vote;
use AppBundle\Form\Type\UploadFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class GalleryController
 * @package AppBundle\Controller
 */
class GalleryController extends Controller
{

    /**
     *
     */
    const MAX_PER_PAGE = 8;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $q           = $request->query->get('q');
        $currentPage = $request->query->get('page', 1);

        $sortBy = $request->query->get('sortBy');
        $whiteList = array('created', 'rating', 'title');
        if (in_array($sortBy, $whiteList) === false) {
            $sortBy = reset($whiteList);
        }
 
        $order     = $request->query->get('order');
        $whiteList = array('desc', 'asc');
        if (in_array($order, $whiteList) === false) {
            $order = reset($whiteList);
        }
 
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Image');
        $query      = $repository->getImages($sortBy, $order, $q);
        $adapter    = new DoctrineORMAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $result     = $pagerfanta->setMaxPerPage(self::MAX_PER_PAGE)
                                 ->setCurrentPage($currentPage)
                                 ->getCurrentPageResults();

        $template = $request->isXmlHttpRequest() ? 'AppBundle:Twig:gallery-content.html.twig' 
                                                 : 'AppBundle:Twig:gallery.html.twig';

        return $this->render($template,
                array(
                 'title'   => 'sandbox|gallery',
                 'content' => $result,
                 'pager'   => $pagerfanta,
                )
        );
 
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @paramConverter("image", class="AppBundle:Image")
     */
    public function imageAction($image)
    {
        $user          = $this->getUser();

        if ($image === null) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->error('Sadly, I could not find the image with id "'.$id.'"');
            return $this->redirectToRoute('_gallery');        
        }
        $entityManager = $this->getDoctrine()->getManager();
        $query  = $entityManager->getRepository('AppBundle:Vote')->countVotes($image)->getQuery();
        $rating = $query->getSingleScalarResult();

        if ($user !== null) {
            $hasVoted = $entityManager->getRepository('AppBundle:Vote')->checkForVote($user, $image);
        } else {
            $hasVoted = false;
        }

        return $this->render('AppBundle:Twig:image.html.twig', array(
            'title'     => 'sandbox|image',
            'image'     => $image,
            'hasVoted'  => $hasVoted,
            'rating'    => $rating
            ));
 
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {
        $image = new Image();

        if ($this->isGranted('create', $image) === false) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->error('You are not authorized to upload an image.');
            return $this->redirectToRoute('_gallery');
        }

        $form = $this->createForm(UploadFormType::class);
        $form->handleRequest($request);

        if ($form->isValid() === true) {
            $data  = $form->getData();
            $image = $this->handleUploadedFile($data, $image);

            $event = new ImageEvent($image, $data);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(ImageEvent::IMAGE_CREATE_EVENT, $event);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();
 
            return $this->redirectToRoute('_gallery');
        }
        return $this->render('AppBundle:Twig:upload.html.twig', array('title' => 'sandbox|project', 'form' => $form->createView()));
 
    }


    /**
     * @param Request $request
     * @paramConverter("image", class="AppBundle:Image")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function imageEditAction(Request $request, $image)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $flash         = $this->get('braincrafted_bootstrap.flash');
        $user          = $this->getUser();
 
        if ($this->isGranted('edit', $image) === false) {
            $flash->error('Sadly, You were not authorized to edit this image.');
            return $this->redirectToRoute('_image', array('id' => $image->getId()));
        } else {
            $hasVoted = $entityManager->getRepository('AppBundle:Vote')->checkForVote($user, $image);
        }
 
        $form = $this->createFormBuilder()
                     ->add('title', TextType::class, array(
                                                   'data' => $image->getTitle(),
                                                   'constraints' => new Length(array('min' => 3), new NotBlank)))
                     ->add('description', TextareaType::class, array('data' => $image->getDescription(), 'required' => true))
                     ->add('Save', SubmitType::class)
                     ->getForm();
 
        $form->handleRequest($request);
 
        if ($form->isValid() === true && $form->isSubmitted() === true) {
            $data = $form->getData();
            $image->setTitle($data['title'])
                  ->setDescription($data['description'])
                  ->setUpdated(new \DateTime()); ;
            $entityManager->flush();
            $flash->success('Image details were edited and changes saved.');

            return $this->redirectToRoute('_image', array('id' => $image->getId()));
        }

        return $this->render('AppBundle:Twig:image.html.twig', array(
            'title'    => 'sandbox|image', 
            'image'    => $image, 
            'hasVoted' => $hasVoted,
            'form'     => $form->createView()));
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function imageDeleteAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $image         = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));
        $flash         = $this->get('braincrafted_bootstrap.flash');
 
        if ($this->isGranted('delete', $image) === false) {
            $flash->error('Sadly, You were not authorized to delete this image.');
            return $this->redirectToRoute('_image', array('id' => $id));
        }
 
        if ($image === null) {
            $flash->error('Sadly, I could not find the image with id "'.$id.'"');
        } else {
            $event = new ImageEvent($image);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(ImageEvent::IMAGE_DELETE_EVENT, $event);

            $entityManager->remove($image);
            $entityManager->flush();

        }
        return $this->redirectToRoute('_gallery');
 
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function imageVoteAction(Request $request)
    {
        $voteValue          = $request->request->get('voteValue');
        $imageId            = $request->request->get('id');
        $entityManager      = $this->getDoctrine()->getManager();
        $image              = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $imageId));
        $user               = $this->getUser();
        $allowedVoteValues  = array(-1, 1);
        $flash              = $this->get('braincrafted_bootstrap.flash');

 
        $voteCheck = $entityManager->getRepository('AppBundle:Vote')->findOneBy(array('user' => $user, 'image' => $image));
 
        if ($this->isGranted('vote', $image) === false || $voteCheck !== null || in_array($voteValue, $allowedVoteValues) === false) {
                $flash->error('Voting access unauthorized, sorry!');
                return $this->redirectToRoute('_gallery');
        }
 
        $vote = new Vote();
        $vote->setImage($image)->setUser($user)->setVote($voteValue);
        $entityManager->persist($vote);
        $entityManager->flush();
        $flash->success('Vote recorded, thanks!');

        return $this->redirectToRoute('_image', array('id' => $imageId));
 
    }


    /**
     * @param $data
     * @param Image $image
     * @return Image
     */
    private function handleUploadedFile($data, Image $image)
    {
        $imageSizeDetails = getimagesize($data['file']->getPathName());
        $imageResolution  = strval($imageSizeDetails[0]).' x '.strval($imageSizeDetails[1]);
        $imageTitle       = $data['title'];
        $imageDescription = $data['description'];
        $newFileName      = sha1(uniqid());
        $fileExtension    = $data['file']->getClientOriginalExtension();
        $fileSize         = $data['file']->getSize();
            
        $image->setPath("/images/".$newFileName.".".$fileExtension)
                ->setSize($fileSize)
                ->setResolution($imageResolution)
                ->setTitle($imageTitle)
                ->setDescription($imageDescription)
                ->setUpdated(new \DateTime())
                ->setCreated(new \DateTime())
                ->setOwner($this->getUser());
        return $image;
    }

}
