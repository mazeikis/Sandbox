<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $recentlyUploaded = $em->getRepository('AppBundle:Image')->GetRecentlyUploaded(4);
        return $this->render('AppBundle:Default:index.html.twig', array('title' => 'sandbox|project', 'recent' => $recentlyUploaded));
    }
    public function aboutAction()
    {
        return $this->render('AppBundle:Default:about.html.twig', array('title' => 'sandbox|about'));
    }
    public function contactAction(Request $request) //not in use currently
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Contact Email')
            ->setFrom('f.mazeikis@gmail.com')
            ->setTo('recipient@example.com')
            ->setBody(
                $this->renderView(
                   'AppBundle:Email:email.txt.twig',
                    array('name' => $name)
                )
            );
        $this->get('mailer')->send($message);
    
        return $this->render('AppBundle:Default:about.html.twig', array('title' => 'sandbox|about'));
    }
}