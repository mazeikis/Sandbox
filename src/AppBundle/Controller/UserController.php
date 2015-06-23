<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Security\ConfirmationTokenGenerator;
use AppBundle\Form\Type\RegistrationFormType;
use AppBundle\Form\Type\VerificationFormType;


class UserController extends Controller
{
    const ITEMS_PER_PAGE = 4;

    public function indexAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $user             = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $slug));
        $recentlyUploaded = $em->getRepository('AppBundle:Image')->getRecentlyUploaded(self::ITEMS_PER_PAGE, $user);
        $test             = $this->generateUrl('_user', array('slug' => $slug), true);

        return $this->render(
            'AppBundle:Twig:user.html.twig',
            array(
             'title'  => 'sandbox|user profile',
             'recent' => $recentlyUploaded,
             'user'   => $user,
             'test'   => $test,
            )
        );

    }//end indexAction()

    public function registrationAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new RegistrationFormType(), $user);
        $form->handleRequest($request);

        if ($form->isValid() === true) {
            $confirmationTokenManager = new ConfirmationTokenGenerator();
            $user->setConfirmationToken($confirmationTokenManager->generateConfirmationToken());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $message = \Swift_Message::newInstance()
            ->setSubject('Verification Email')
            ->setFrom('robot@codesandbox.info')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'AppBundle:Email:registration.txt.twig',
                    array('link' => $user->getConfirmationToken())
                )
            );
            $this->get('mailer')->send($message);
            $request->getSession()
                ->getFlashBag()
                ->add(
                    'success',
                    'Rergistration almost complete,
                    please check Your email
                    for verification link to finish registration process!'
                );
            return $this->redirectToRoute('_home');
        } else {
            $message = null;
        }//end if
        return $this->render(
            'AppBundle:Twig:registration.html.twig',
            array(
             'title'   => 'sandbox|project',
             'message' => $message,
             'form'    => $form->createView()
            )
        );

    }//end indexAction()

    public function emailVerificationAction(Request $request, $confirmationToken)
    {
        $em   = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('confirmationToken' => $confirmationToken));
        if ($user === false) {
            $request->getSession()
                    ->getFlashBag()
                    ->add('warning', 'Oops, no user with matching token found!');
            return $this->redirectToRoute('_home');
        } else {
            $user->setEnabled(true)->setConfirmationToken(null);
            $em->flush();
            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'User '.$user->getUsername().' verified successfully!');
            return $this->redirectToRoute('_home');
        }//end if

    }//end emailVerificationAction()

    public function passwordResetRequestAction(Request $request)
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

            if ($user !== null) {
                $confirmationTokenManager = new confirmationTokenGenerator();
                $user->setConfirmationToken($confirmationTokenManager->generateConfirmationToken());
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
            } else {
                    $request->getSession()
                    ->getFlashBag()
                    ->add('warning', 'Oops, no user with matching token found!');
                return $this->redirectToRoute('_home');
            }//end if
        }//end if
        return $this->render(
            'AppBundle:Twig:reset.html.twig',
            array(
             'title'       => 'sandbox|project',
             'requestForm' => $form->createView(),
            )
        );

    }//end passwordResetRequestAction()


    public function passwordResetVerificationAction(Request $request, $confirmationToken)
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

    }//end passwordResetVerificationAction()

}//end class
