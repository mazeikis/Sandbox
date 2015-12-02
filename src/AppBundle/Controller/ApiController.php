<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ApiController extends Controller
{
	public function defaultAction(Request $request)
	{
        return new Response('', Response::HTTP_OK);
	}
	const MAX_PER_PAGE = 8;
 
    public function galleryAction(Request $request)
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
        $pagerfanta->setMaxPerPage(self::MAX_PER_PAGE)->setCurrentPage($currentPage);
		$result 	= $pagerfanta->getCurrentPageResults();
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $gallery = array();

        foreach($result as $image){
        	$gallery[] = array(
        	'thumbLink'        => $cacheManager->getBrowserPath($image[0]->getPath(), 'thumb'),
        	'imageTitle'       => $image[0]->getTitle(),
        	'imageRating'      => $image['votes_sum']
        	);
        }

		$url  = $this->get('router')->generate('_api_gallery', array(), true);
        $data = array( 'currentPage' => $currentPage, 'sortedBy' => $sortBy, 'order' => $order, 'gallery' => $gallery);

        $response = new Response(json_encode($data), Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Location', $url);

        return $response;

    }

    public function imageAction(Request $request, $id)
    {
    	$user          = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $image         = $entityManager->getRepository('AppBundle:Image')->findOneBy(array('id' => $id));

        if ($image === null) {
            $data 	  = array('error' => 'Image file with id "'.$id.'" not found.');
            //$url  	  = $this->get('router')->generate('_api_image', array('id' => $id), true);
			$response = new Response(json_encode($data), Response::HTTP_NOT_FOUND);
        	$response->headers->set('Content-Type', 'application/json');
        	return $response;
        }
 
        $rating = $entityManager->getRepository('AppBundle:Vote')
        						->countVotes($image)
        						->getQuery()
        						->getSingleScalarResult();

        if ($user !== null) {
            $data['votePresent']  = $entityManager->getRepository('AppBundle:Vote')->checkForVote($user, $image);
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
		$response = new Response(json_encode($data), Response::HTTP_FOUND);
        //$response->headers->set('Content-Type', 'application/json');
        return $response;
         
    }
}
