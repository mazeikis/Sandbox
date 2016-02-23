<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Filesystem\Filesystem;
use AppBundle\Entity\Vote;


class ApiController extends Controller
{


    const MAX_PER_PAGE = 8;


	public function defaultAction(Request $request)
	{
        $data = array('message'=> 'Welcome to CodeSandbox API.');
        return new JsonResponse($data, Response::HTTP_OK);
	}
 
    public function galleryAction(Request $request)
    {
        $q = $request->query->get('q');

        $currentPage = max( 1, $request->query->getInt('page'));
 
        $sortBy    = $request->query->get('sortBy', 'created');
        $whiteList = array('created', 'rating', 'title');
        if (in_array($sortBy, $whiteList) === false) {
            $data = array('error' => "Invalid request parameter 'sortBy'.");
            return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
        }
 
        $order     = $request->query->get('order', 'desc');
        $whiteList = array('desc', 'asc');
        if (in_array($order, $whiteList) === false) {
            $data = array('error' => "Invalid request parameter 'order'.");
            return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
        }
 
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Image');
        $query      = $repository->getImages($sortBy, $order, $q);
 
        $adapter    = new DoctrineORMAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        

        if($currentPage > $pagerfanta->getNbPages()) {
            $data = array('error' => "Page $currentPage not found.");
            return new JsonResponse($data, Response::HTTP_NOT_FOUND);
        }
        
        $pagerfanta->setMaxPerPage(self::MAX_PER_PAGE)->setCurrentPage($currentPage);
        
        $result       = $pagerfanta->getCurrentPageResults();
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $gallery      = array();

        foreach($result as $image){
        	$gallery[] = array(
        	'thumbLink'   => $cacheManager->getBrowserPath($image[0]->getPath(), 'thumb'),
        	'imageTitle'  => $image[0]->getTitle(),
        	'imageRating' => $image['votes_sum']
        	);
        }

        $data = array(
            'currentPage' => $currentPage, 
            'sortedBy' => $sortBy, 
            'order' => $order, 
            'gallery' => $gallery
            );

        $response = new JsonResponse($data, Response::HTTP_OK);

        return $response;

    }

    public function imageAction(Request $request, $id)
    {
    	$user          = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $image         = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));

        if(!preg_match('/^\d+$/', $id)) {
            $data = array('error' => "id value is invalid, only positive integers are accepted.");
            return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
        }

        if ($image === null) {
            $data = array('error' => "Image file with id $id not found.");
        	return new JsonResponse($data, Response::HTTP_NOT_FOUND);
        }
 
        $rating = $entityManager->getRepository('AppBundle:Vote')
        						->countVotes($image)
        						->getQuery()
        						->getSingleScalarResult();

        if ($user !== null) {
            $data['hasUserVoted'] = $entityManager->getRepository('AppBundle:Vote')->checkForVote($user, $image);
        } 

        $data = array('imageId' 	 => $image->getId(), 
        			  'fullSizeLink' => $request->getUriForPath($image->getPath()),
        			  'title' 		 => $image->getTitle(),
        			  'description'  => $image->getDescription(),
        			  'resolution' 	 => $image->getResolution(),
        			  'created' 	 => $image->getCreated()->format("d-M-Y H:i:s"),
        			  'updated' 	 => $image->getUpdated()->format("d-M-Y H:i:s"),
        			  'owner' 		 => $image->getOwner()->getUsername(),
        			  'rating' 		 => $rating
        			  );

        return new JsonResponse($data, Response::HTTP_OK);
         
    }

    public function imageDeleteAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $image         = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));
 
        if ($image === null) {
            $data = array('error' => "Image file with id $id not found.");
            return new JsonResponse($data, Response::HTTP_NOT_FOUND);
        } 
        if ($this->isGranted('delete', $image) === false) {
            $data = array('error' => "You are not authorized to delete this image.");
            return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        }
 
        

        $fileSystem = new Filesystem();
        $imageDir   = $this->getImageDir();
        $fileSystem->remove($imageDir.$image->getPath());

        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $cacheManager->remove($image->getPath());

        $entityManager->remove($image);
        $entityManager->flush();

        $data = array('message' => "Image was successfully deleted.");
        return new JsonResponse($data, Response::HTTP_OK);
 
    }

    public function imageVoteAction(Request $request)
    {
        $voteValue     = $request->request->get('voteValue');
        $id            = $request->request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $user          = $this->getUser();

        $image = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));
        if($image === null){
            $data = array('error' => "Image file with id $id not found.");
            return new JsonResponse($data, Response::HTTP_NOT_FOUND);
        }

        if($voteValue != 1 && $voteValue != -1){
            $data = array('error' => "Vote value of $voteValue is invalid. Use '1' to upvote or '-1' to downvote an image.");
            return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
        }
 
        $voteCheck = $entityManager->getRepository('AppBundle:Vote')->findOneBy(array('user' => $user, 'image' => $image));        
        if($voteCheck !== null){
            $data = array('error' => "User ".$user->getUsername()." has already voted on image ".$image->getId());
            return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        }

        if ($this->isGranted('vote', $image) === false) {
            $data = array('error' => "Voting access unauthorized, sorry!");
            return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        }
 
        $vote = new Vote();
        $vote->setImage($image)->setUser($user)->setVote($voteValue);
        $entityManager->persist($vote);
        $entityManager->flush();

 
        $data = array('message' => "Vote recorded successfully.");
        return new JsonResponse($data, Response::HTTP_OK);
 
    }

    private function getImageDir()
    {
        return $this->get('kernel')->getRootDir().'/../web/'.$this->getRequest()->getBasePath();
    }
 
}
