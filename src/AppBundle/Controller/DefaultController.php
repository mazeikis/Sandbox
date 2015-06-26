<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\ContactFormType;


class DefaultController extends Controller
{

    const ITEMS_PER_PAGE = 4;

    public function indexAction()
    {
        $em     = $this->getDoctrine()->getManager();
        $recent = $em->getRepository('AppBundle:Image')->getRecentlyUploaded(self::ITEMS_PER_PAGE);

        return $this->render('AppBundle:Twig:index.html.twig', array('title' => 'sandbox|project', 'recent' => $recent));

    }//end indexAction()


    public function aboutAction(Request $request)
    {
        $formType = new ContactFormType();
        $form     = $this->createForm($formType);

        $form->handleRequest($request);

        if ($form->isValid() === true) {
            $data    = $form->getData();
            $message = \Swift_Message::newInstance()
            ->setSubject($data['subject'])
            ->setFrom($data['from'])
            ->setTo('robot@codesandbox.info')
            ->setBody(
                $this->renderView(
                    'AppBundle:Email:contact.txt.twig',
                    array(
                     'message' => $data['message'],
                     'user'    => $this->getUser(),
                    )
                )
            );
            $this->get('mailer')->send($message);

            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->error('Message sent, thank you!');

            return $this->redirectToRoute('_about');
        }

        return $this->render('AppBundle:Twig:about.html.twig', array('title' => 'sandbox|about', 'form' => $form->createView()));

    }//end aboutAction()


}//end class
