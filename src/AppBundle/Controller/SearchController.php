<?php

namespace AppBundle\Controller;

use AppBundle\Helpers\PageManager;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Image;

class SearchController extends Controller
{
	public function indexAction(Request $request )
	{
		
		$q = $request->query->get('q');
		$resultsPerPage = 8;
		if($q){
        	$em = $this->getDoctrine()->getManager();
            $totalRows = $em->getRepository('AppBundle:Image')->CountResultRows($q);
   			$pageManager = new PageManager($request, $totalRows, $resultsPerPage);
			$query = $em->getRepository('AppBundle:Image')
						 ->SearchForQuery($q, $pageManager->getResultsPerPage(),
						                      $pageManager->getStartingItem(), 
											  $pageManager->getSortBy(), 
											  $pageManager->getOrder()
											  );
		}else{
			$query = null;
			$pageManager = new PageManager($request);
		}
        return $this->render('AppBundle:Twig:search.html.twig', array(
        	'title' => 'sandbox|project',
        	'content' => $query, 
        	'pageManager' => $pageManager));
	}
}