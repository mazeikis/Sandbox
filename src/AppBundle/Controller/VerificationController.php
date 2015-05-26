<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Security\ConfirmationTokenGenerator;
use AppBundle\Form\Type\VerificationFormType;

class VerificationController extends Controller
{


    public function emailAction(Request $request, $confirmationToken)
    {
        $em   = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('confirmationToken' => $confirmationToken));
        if ($user === false) {
            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Oops, no matching token found!');
            return $this->redirectToRoute('_home');
        } else {
            $user->setEnabled(true)->setConfirmationToken(null);
            $em->flush();
            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'User '.$user->getUsername().' verified successfully!');
            return $this->redirectToRoute('_home');
        }//end if

    }//end emailAction()


    public function resetAction(Request $request)
    {
        $tempData = array();
        $form     = $this->createFormBuilder($tempData)
            ->add('username', 'text', array('label' => 'Your username: ', 'required' => true))
            ->add('reset password', 'submit')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid() === true) {
            $em   = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('username' => $data['username']));

            $confirmationTokenManager = new confirmationTokenGenerator();
            $user->setConfirmationToken($confirmationTokenManager->generateConfirmationToken());
            $em->persist($user);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Password reset Email')
                ->setFrom('robot@codesandbox.info')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('AppBundle:Email:reset.txt.twig', array('link' => $user->getConfirmationToken())));

            $this->get('mailer')->send($message);

            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'User '.$user->getUsername().' reset email sent successfuly!');
            return $this->redirectToRoute('_home');
        }//end if
        return $this->render(
            'AppBundle:Twig:reset.html.twig',
            array(
             'title'       => 'sandbox|project',
             'requestForm' => $form->createView(),
            )
        );

    }//end resetAction()


    public function verifyResetAction(Request $request, $confirmationToken)
    {
        $em   = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(['confirmationToken' => $confirmationToken]);
        if ($user === false) {
            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Oops, no matching user found!');
            return $this->redirectToRoute('_home');
        }//end if
        $form = $this->createForm(new VerificationFormType());
        $form->handleRequest($request);
        if ($form->isValid() === true) {
            $data = $form->getData();
            $user->setPlainPassword($data['plainPassword']);
            $user->setConfirmationToken(null);
            $em->flush();
            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Users '.$user->getUsername().' password changed successfully!');
            return $this->redirectToRoute('_user', array('slug' => $user->getId()));
        }//end if
        return $this->render(
            'AppBundle:Twig:reset.html.twig',
            array(
             'title'      => 'sandbox|project',
             'submitForm' => $form->createView(),
            )
        );

    }//end verifyResetAction()


}//end class
