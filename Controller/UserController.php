<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class UserController extends Controller
{
	public function indexAction($slug)
	{
		$em = $this->getDoctrine()->getEntityManager();

        $recentlyUploaded = $em->getRepository('AppBundle:Image')->getRecentlyUploaded(5, $slug);
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $slug));

		return $this->render('AppBundle:Default:user.html.twig', array('title' => 'sandbox|user profile', 'recent' => $recentlyUploaded, 'user' => $user, 'test' => $em->getRepository('SandboxBundle:User')->test()));

	}
}