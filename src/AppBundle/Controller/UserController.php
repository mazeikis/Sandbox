<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
	public function indexAction($slug)
	{
		$em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $slug));
        $recentlyUploaded = $em->getRepository('AppBundle:Image')->getRecentlyUploaded(5, $user);

		return $this->render('AppBundle:Twig:user.html.twig', array('title' => 'sandbox|user profile', 'recent' => $recentlyUploaded, 'user' => $user));

	}
}