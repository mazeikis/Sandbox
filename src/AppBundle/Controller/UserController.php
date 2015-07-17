<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Security\ConfirmationTokenGenerator;
use AppBundle\Form\Type\RegistrationFormType;
use AppBundle\Form\Type\VerificationFormType;
use AppBundle\Form\Type\PasswordChangeFormType;
use AppBundle\Form\Type\EmailChangeFormType;


class UserController extends Controller
{
    const ITEMS_PER_PAGE = 4;

    public function indexAction(Request $request, $slug)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user             = $entityManager->getRepository('AppBundle:User')->findOneBy(array('id' => $slug));
        $recentlyUploaded = $entityManager->getRepository('AppBundle:Image')->getRecentlyUploaded(self::ITEMS_PER_PAGE, $user);

        $passwordForm = $this->createForm(new PasswordChangeFormType());
        $emailForm = $this->createForm(new EmailChangeFormType());

        $emailForm->handleRequest($request);
        $passwordForm->handleRequest($request);

        if($user === $this->getUser()) {
            $this->handleForm($emailForm, $passwordForm);
        }

        return $this->render(
            'AppBundle:Twig:user.html.twig',
            array(
             'title'        => 'sandbox|user profile',
             'recent'       => $recentlyUploaded,
             'user'         => $user,
             'passwordForm' => $passwordForm->createView(),
             'emailForm'    => $emailForm->createView()
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

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $message = \Swift_Message::newInstance()
            ->setContentType("text/html")
            ->setSubject('codeSandbox Verification Email')
            ->setFrom('robot@codesandbox.info')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'AppBundle:Email:registration.txt.twig',
                    array('link' => $user->getConfirmationToken())
                )
            );
            $this->get('mailer')->send($message);
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('Registration submitted, please check Your email and finish registration progress.');

            return $this->redirectToRoute('_home');
        }//end if

        return $this->render(
            'AppBundle:Twig:registration.html.twig',
            array(
             'title'   => 'sandbox|project',
             'form'    => $form->createView()
            )
        );

    }//end indexAction()

    public function emailVerificationAction(Request $request, $confirmationToken)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $flash         = $this->get('braincrafted_bootstrap.flash');

        if($confirmationToken === null){
            $flash->error('Invalid verification token!');
            return $this->redirectToRoute('_home');
        }

        $user = $entityManager->getRepository('AppBundle:User')->findOneBy(array('confirmationToken' => $confirmationToken));

        if ($user === false) {
            $flash->error('Oops, no user with matching token found!');
        } else {
            $user->setEnabled(true)->setConfirmationToken(null);
            $entityManager->flush();

            $flash->success('User '.$user->getUsername().' verified successfully!');

        }//end if
        return $this->redirectToRoute('_home');

    }//end emailVerificationAction()

    public function passwordResetRequestAction(Request $request)
    {
        $form = $this->createFormBuilder()
                     ->add('username', 'text', array('label' => 'Your username: ', 'required' => true))
                     ->add('reset password', 'submit')
                     ->getForm();

        $form->handleRequest($request);
        if ($form->isValid() === true) {
            $entityManager = $this->getDoctrine()->getManager();
            $data          = $form->getData();
            $user          = $entityManager->getRepository('AppBundle:User')->findOneBy(array('username' => $data['username']));
            $flash         = $this->get('braincrafted_bootstrap.flash');

            if ($user !== null) {
                $confirmationTokenManager = new confirmationTokenGenerator();
                $user->setConfirmationToken($confirmationTokenManager->generateConfirmationToken());
                $entityManager->flush();

                $message = \Swift_Message::newInstance()
                    ->setContentType("text/html")
                    ->setSubject('codeSandbox Password reset Email')
                    ->setFrom('robot@codesandbox.info')
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('AppBundle:Email:reset.txt.twig', array('link' => $user->getConfirmationToken())));
                $this->get('mailer')->send($message);

                $flash->success('User '.$user->getUsername().' reset email sent successfuly!');
            } else {
                $flash->error('Oops, no user with matching token found!');
            }//end if
            return $this->redirectToRoute('_home');
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
        $entityManager = $this->getDoctrine()->getManager();
        $user          = $entityManager->getRepository('AppBundle:User')->findOneBy(['confirmationToken' => $confirmationToken]);
        $flash         = $this->get('braincrafted_bootstrap.flash');

        if ($user === false) {
            $flash->error('Oops, no user with matching token found!');
            return $this->redirectToRoute('_home');
        }//end if

        $form = $this->createForm(new VerificationFormType());
        $form->handleRequest($request);

        if ($form->isValid() === true) {
            $data = $form->getData();
            $user->setPlainPassword($data['plainPassword']);
            $user->setConfirmationToken(0);
            $entityManager->flush();
            $flash->success('Password for '.$user->getUsername().' was changed successfully!');
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


    private function handleForm($emailForm, $passwordForm)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $flash         = $this->get('braincrafted_bootstrap.flash');
        $user          = $this->getUser();

        if($emailForm->isValid() === true){
            $data = $emailForm->getData();
            $user->setEmail($data['email']);
            $flash->success('User '.$user->getUsername().' email was changed successfully!');
        } else {
            $error = (string) $emailForm->getErrors(true);
            if (empty($error) === false) {
                $flash->error($error);
                return $this->redirectToRoute('_user', array('slug' => $user->getId()));
            }//end if
        }//end if


        if($passwordForm->isValid() === true) {
            $data = $passwordForm->getData();
            $user->setPlainPassword($data['plainPassword']);
            $flash->success('User '.$user->getUsername().' password was changed successfully!');
        } else {
            $error = (string) $passwordForm->getErrors(true);
            if (empty($error) === false) {
                $flash->error($error);
                return $this->redirectToRoute('_user', array('slug' => $user->getId()));
            }//end if
        }//end if

        if(empty($data) === false) {
            $entityManager->flush();
            return $this->redirectToRoute('_user', array('slug' => $user->getId()));
        }//end if

    }

}//end class
