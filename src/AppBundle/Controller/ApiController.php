<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use AppBundle\Entity\Vote;
use AppBundle\Entity\Image;
use AppBundle\Event\ImageEvent;

/**
 * Class ApiController
 * @package AppBundle\Controller
 */
class ApiController extends Controller
{


    /**
     * Maximum items (images) per page, required for pagerfanta.
     */
    const MAX_PER_PAGE = 8;


    /**
     * @return JsonResponse
     */
    public function defaultAction()
	{
        $message = array('message'=> 'Welcome to CodeSandbox API.');
        return new JsonResponse($message, Response::HTTP_OK);
	}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function galleryAction(Request $request)
    {
        $q = $request->query->get('q');

        $currentPage = $request->query->getInt('page', 1);
        if(!is_int($currentPage) || $currentPage < 0){
            $message = array('error' => "Invalid page number format.");
            return new JsonResponse($message, Response::HTTP_BAD_REQUEST);
        }

        $sortBy    = $request->query->get('sortBy', 'created');
        $whiteList = array('created', 'rating', 'title');
        if (in_array($sortBy, $whiteList) === false) {
            $message = array('error' => "Invalid request parameter 'sortBy'.");
            return new JsonResponse($message, Response::HTTP_BAD_REQUEST);
        }
 
        $order     = $request->query->get('order', 'desc');
        $whiteList = array('desc', 'asc');
        if (in_array($order, $whiteList) === false) {
            $message = array('error' => "Invalid request parameter 'order'.");
            return new JsonResponse($message, Response::HTTP_BAD_REQUEST);
        }
 
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Image');
        $query      = $repository->getImages($sortBy, $order, $q);
 
        $adapter    = new DoctrineORMAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        

        if ($currentPage > $pagerfanta->getNbPages()) {
            $message = array('error' => "Page $currentPage not found.");
            return new JsonResponse($message, Response::HTTP_NOT_FOUND);
        }
        
        $pagerfanta->setMaxPerPage(self::MAX_PER_PAGE)->setCurrentPage($currentPage);
        
        $result       = $pagerfanta->getCurrentPageResults();
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $gallery      = array();

        foreach ($result as $image) {
        	$gallery[] = array(
            'id'          => $image[0]->getId(),
        	'thumbLink'   => $cacheManager->getBrowserPath($image[0]->getFile(), 'thumb'),
        	'imageTitle'  => $image[0]->getTitle(),
        	'imageRating' => $image['votes_sum']
        	);
        }

        $message = array(
            'currentPage' => $currentPage, 
            'sortedBy' => $sortBy,
            'order' => $order,
            'gallery' => $gallery
            );

        $response = new JsonResponse($message, Response::HTTP_OK);

        return $response;

    }

    /**
     * @param Request $request
     * @ParamConverter("image", class="AppBundle:Image")
     * @return JsonResponse
     */
    public function imageAction(Request $request, Image $image)
    {
    	$user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $rating = $entityManager->getRepository('AppBundle:Vote')
        						->countVotes($image)
        						->getQuery()
        						->getSingleScalarResult();

        if ($user !== null) {
            $message['hasUserVoted'] = $entityManager->getRepository('AppBundle:Vote')->checkForVote($user, $image);
        } 

        $message = array(
                      'imageId' 	 => $image->getId(), 
        			  'fullSizeLink' => $request->getUriForPath($this->getParameter('image_directory').'/'.$image->getFile()),
        			  'title' 		 => $image->getTitle(),
        			  'description'  => $image->getDescription(),
        			  'resolution' 	 => $image->getResolution(),
        			  'created' 	 => $image->getCreated()->format("d-M-Y H:i:s"),
        			  'updated' 	 => $image->getUpdated()->format("d-M-Y H:i:s"),
        			  'owner' 		 => $image->getOwner()->getUsername(),
        			  'rating' 		 => $rating,
        			  );

        return new JsonResponse($message, Response::HTTP_OK);
         
    }

    /**
     * @ParamConverter("image", class="AppBundle:Image")
     * @return JsonResponse
     */
    public function imageDeleteAction($image)
    {
        if ($this->isGranted('delete', $image) === false) {
            $message = array('error' => "You are not authorized to delete this image.");
            return new JsonResponse($message, Response::HTTP_UNAUTHORIZED);
        }

        $event = new ImageEvent($image);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(ImageEvent::IMAGE_DELETE_EVENT, $event);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($image);
        $entityManager->flush();

        $message = array('message' => "Image was successfully deleted.");
        return new JsonResponse($message, Response::HTTP_OK);
 
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function imageVoteAction(Request $request)
    {
        $voteValue     = $request->request->get('voteValue');
        $id            = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $user          = $this->getUser();

        $image = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));
        if ($image === null) {
            $message = array('error' => "Image file with id $id not found.");
            return new JsonResponse($message, Response::HTTP_NOT_FOUND);
        }

        if ($this->isGranted('vote', $image) === false) {
            $message = array('error' => "Voting access unauthorized, sorry!");
            return new JsonResponse($message, Response::HTTP_UNAUTHORIZED);
        }

        if ($voteValue != 1 && $voteValue != -1) {
            $message = array('error' => "Vote value of $voteValue is invalid. Use '1' to upvote or '-1' to downvote an image.");
            return new JsonResponse($message, Response::HTTP_BAD_REQUEST);
        }
 
        $voteCheck = $entityManager->getRepository('AppBundle:Vote')->findOneBy(array('user' => $user, 'image' => $image));        
        if ($voteCheck !== null) {
            $message = array('error' => "User ".$user->getUsername()." has already voted on image ".$image->getId());
            return new JsonResponse($message, Response::HTTP_UNAUTHORIZED);
        }
 
        $vote = new Vote();
        $vote->setImage($image)->setUser($user)->setVote($voteValue);
        $entityManager->persist($vote);
        $entityManager->flush();

        $message = array('message' => "Vote recorded successfully.");
        return new JsonResponse($message, Response::HTTP_OK);
 
    }

    /**
     *
     * @return string
     */
    private function getImageDir()
    {
        return $this->get('kernel')->getRootDir().'/../web';
    }
 
}
