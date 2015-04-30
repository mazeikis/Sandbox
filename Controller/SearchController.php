<?php

namespace AppBundle\Controller;

use AppBundle\Helpers\Paginator;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Image;



class SearchController extends Controller
{
	public function indexAction(Request $request )
	{
		$q = $request->query->get('q');
		$result = null;
		
		if($q){/*
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery(
    			'SELECT image
    			FROM SandboxBundle:Image image 
    			LEFT JOIN image.owner user
    			WHERE image.title LIKE :key
    			OR image.description LIKE :key 
    			OR user.username LIKE :key 
    			ORDER BY image.created ASC'
				)->setParameter('key', '%'.$q.'%');
				$result = $query->getResult();*/
			$em = $this->getDoctrine()->getEntityManager();
			$result = $em->getRepository('AppBundle:Image')->SearchForQuery($q);
		}
		$countResults = count($result);
        return $this->render('AppBundle:Default:search.html.twig', array('title' => 'sandbox|project', 'result' => $result));
	}
}