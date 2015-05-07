<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\ContactFormType;


class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $recentlyUploaded = $em->getRepository('AppBundle:Image')->getRecentlyUploaded(4);
        
        return $this->render('AppBundle:Twig:index.html.twig', array('title' => 'sandbox|project', 'recent' => $recentlyUploaded));
    }
    public function aboutAction(Request $request)
    {
        $form = $this->createForm(new ContactFormType());
        
        $form->handleRequest($request);

        if($form->isValid()){
            $data = $form->getData();
            $message = \Swift_Message::newInstance()
                ->setSubject($data['subject'])
                ->setFrom($data['from'])
                ->setTo('robot@codesandbox.info')
                ->setBody(
                    $this->renderView(
                       'AppBundle:Email:contact.txt.twig',
                        array('message' => $data['message'], 'user' => $this->getUser())
                    )
                );
            $this->get('mailer')->send($message);
            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Message sent, thank you!');
            return $this->redirectToRoute('_about');
        }
        return $this->render('AppBundle:Twig:about.html.twig', array('title' => 'sandbox|about', 'form' => $form->createView()));
    }
}
