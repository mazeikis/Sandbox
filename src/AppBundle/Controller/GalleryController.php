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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        $sortBy      = $request->query->get('sortBy', 'created');
        $order       = $request->query->get('order', 'desc');

 
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
     * @ParamConverter("image", class="AppBundle:Image")
     */
    public function imageAction($image)
    {
        $user          = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $query         = $entityManager->getRepository('AppBundle:Vote')->countVotes($image)->getQuery();
        $rating        = $query->getSingleScalarResult();

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

        $form = $this->createForm(UploadFormType::class, $image);
        $form->handleRequest($request);

        if ($form->isValid() === true) {
            $user = $this->getUser();
            $image->setOwner($user);
            $event = new ImageEvent($image);
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
     * @ParamConverter("image", class="AppBundle:Image")
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
     * @ParamConverter("image", class="AppBundle:Image")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function imageDeleteAction($image)
    {
        $entityManager = $this->getDoctrine()->getManager();
 
        if ($this->isGranted('delete', $image) === false) {
            throw new AccessDeniedException();
        }

        $event = new ImageEvent($image);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(ImageEvent::IMAGE_DELETE_EVENT, $event);

        $entityManager->remove($image);
        $entityManager->flush();

        return $this->redirectToRoute('_gallery');
 
    }


    /**
     * @param Request $request
     * @param Image $image
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("image", class="AppBundle:Image")
     */
    public function imageVoteAction(Request $request, Image $image)
    {
        $voteValue         = $request->request->get('voteValue');
        $entityManager     = $this->getDoctrine()->getManager();
        $user              = $this->getUser();
        $allowedVoteValues = array(-1, 1);
        $flash             = $this->get('braincrafted_bootstrap.flash');

        if ($user === null || in_array($voteValue, $allowedVoteValues) === false) {
            throw new AccessDeniedException();
        }
 
        $voteCheck = $entityManager->getRepository('AppBundle:Vote')->checkForVote($user, $image);

        if ($this->isGranted('vote', $image) === false || $voteCheck !== false) {
            throw new AccessDeniedException();
        }
 
        $vote = new Vote();
        $vote->setImage($image)->setUser($user)->setVote($voteValue);
        $entityManager->persist($vote);
        $entityManager->flush();
        $flash->success('Vote recorded, thanks!');

        return $this->redirectToRoute('_image', array('id' => $image->getId()));
 
    }

}
