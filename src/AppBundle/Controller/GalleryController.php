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
use Symfony\Component\Filesystem\Filesystem;
 
class GalleryController extends Controller
{
 
    const MAX_PER_PAGE = 8;
 
    public function indexAction(Request $request)
    {
        $q           = $request->query->get('q');
        $currentPage = max( 1, $request->query->get('page'));
 
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

        return $this->render( $template,
                array(
                 'title'   => 'sandbox|gallery',
                 'content' => $result,
                 'pager'   => $pagerfanta,
                )
        );
 
    }
 
 
    public function imageAction($id)
    {
        $user          = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $image         = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));

        if ($image === null) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->error('Sadly, I could not find the image with id "' . $id . '"');
            return $this->redirectToRoute('_gallery');        
        }
 
        $query  = $entityManager->getRepository('AppBundle:Vote')->countVotes($image)->getQuery();
        $rating = $query->getSingleScalarResult();

        if ($user !== null) {
            $hasVoted  = $entityManager->getRepository('AppBundle:Vote')->checkForVote($user, $image);
        } else {
            $hasVoted = false;
        }

        return $this->render('AppBundle:Twig:image.html.twig', array(
            'title'     => 'sandbox|image',
            'image'     => $image,
            'hasVoted'  => $hasVoted,
            'rating' => $rating
            ));
 
    }
 
 
    public function uploadAction(Request $request)
    {
        $image = new Image();
        $user  = $this->getUser();
        $flash = $this->get('braincrafted_bootstrap.flash');

        $form  = $this->createForm(new UploadFormType());
        $form->handleRequest($request);

        if ($this->get('security.authorization_checker')->isGranted('create', $image, $user) === false) {
            $flash->error('You are not authorized to upload an image.');
            return $this->redirectToRoute('_gallery');
        }

        if ($form->isValid() === true) {
            $data = $form->getData();
            $this->handleUploadedFile($data, $image);
            $image->setOwner($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();
 
            $flash->success('Image sucessfully uploaded!');
            return $this->redirectToRoute('_gallery');
        }
        return $this->render('AppBundle:Twig:upload.html.twig', array('title' => 'sandbox|project', 'form' => $form->createView()));
 
    }
 
 
    public function imageEditAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $image         = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));
        $flash         = $this->get('braincrafted_bootstrap.flash');
        $user          = $this->getUser();
 
        if ($this->get('security.authorization_checker')->isGranted('edit', $image, $user) === false) {
            $flash->error('Sadly, You were not authorized to edit this image.');
            return $this->redirectToRoute('_image', array('id' => $id));
        }
 
        $form = $this->createFormBuilder()
                     ->add('title', 'text', array(
                                                   'data' => $image->getTitle(),
                                                   'constraints' => new Length(array('min' => 3), new NotBlank)))
                     ->add('description', 'textarea', array( 'data' => $image->getDescription(), 'required' => true))
                     ->add('Save', 'submit')
                     ->getForm();
 
        $form->handleRequest($request);
 
        if ($form->isValid() === true) {
            $data = $form->getData();
            $image->setTitle($data['title'])->setDescription($data['description']);
            $entityManager->flush();
            $flash->success('Image details were edited and changes saved.');
 
            return $this->redirectToRoute('_image', array('id' => $id));
        }
 
        return $this->render('AppBundle:Twig:image.html.twig', array('title' => 'sandbox|image', 'image' => $image, 'form' => $form->createView()));
 
    }
 
 
    public function imageDeleteAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $image         = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));
        $flash         = $this->get('braincrafted_bootstrap.flash');
 
        if ($this->get('security.authorization_checker')->isGranted('delete', $image) === false) {
            $flash->error('Sadly, You were not authorized to delete this image.');
            return $this->redirectToRoute('_image', array('id' => $id));
        }
 
        if ($image === null) {
            $flash->error('Sadly, I could not find the image with id "' . $id . '"');
        } else {
            $fileSystem = new Filesystem();
            $imageDir   = $this->getImageDir();
            $fileSystem->remove($imageDir.$image->getPath());

            $cacheManager = $this->container->get('liip_imagine.cache.manager');
            $cacheManager->remove($image->getPath());

            $entityManager->remove($image);
            $entityManager->flush();

            $flash->alert('Image was successfully deleted.');
 
        }
        return $this->redirectToRoute('_gallery');
 
    }
 
 
    public function imageVoteAction(Request $request)
    {
        $voteValue     = $request->request->get('voteValue');
        $imageId       = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $image         = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $imageId));
        $user          = $this->getUser();
        $voteWhiteList = array(-1, 1);
        $flash         = $this->get('braincrafted_bootstrap.flash');

 
        $voteCheck = $entityManager->getRepository('AppBundle:Vote')->findOneBy(array('user' => $user, 'image' => $image));
 
        if ($this->get('security.authorization_checker')->isGranted('vote', $image, $user) === false || $voteCheck !== null || in_array($voteValue, $voteWhiteList) === false) {
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
 

    private function handleUploadedFile($data, Image $image)
    {
        $imageSizeDetails = getimagesize($data['file']->getPathName());
        $imageResolution  = strval($imageSizeDetails[0]).' x '.strval($imageSizeDetails[1]);
        $imageTitle       = $data['title'];
        $imageDescription = $data['description'];
        $newFileName      = sha1(uniqid());
        $fileExtension    = $data['file']->getClientOriginalExtension();
        $fileSize         = $data['file']->getSize();
            
        $image->setPath("images/".$newFileName.".".$fileExtension)
              ->setSize($fileSize)
              ->setResolution($imageResolution)
              ->setTitle($imageTitle)
              ->setDescription($imageDescription);

        $imageDir = $this->getImageDir();
        $data['file']->move($imageDir.'images/', $image->getPath());
    }


    private function getImageDir()
    {
        return $this->get('kernel')->getRootDir().'/../web/'.$this->getRequest()->getBasePath();
    }
 
}
