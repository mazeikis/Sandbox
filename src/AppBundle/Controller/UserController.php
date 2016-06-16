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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class UserController extends Controller
{
    const ITEMS_PER_PAGE = 4;

    public function indexAction(Request $request, $userId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user   = $entityManager->getRepository('AppBundle:User')
                                ->findOneBy(array('id' => $userId));
        $recent = $entityManager->getRepository('AppBundle:Image')
                                ->findBy(array('owner' => $user), array('created' => 'DESC'), self::ITEMS_PER_PAGE);


        $passwordForm = $this->createForm(PasswordChangeFormType::class);
        $emailForm    = $this->createForm(EmailChangeFormType::class);

        if($user === $this->getUser()) {
            if($user->getEnabled() === false){
                $flash = $this->get('braincrafted_bootstrap.flash');
                $flash->error('This user account has not been verified yet. Please check Your email for verification link or use "Resend Verification Link" button bellow!');
            }

            $emailForm->handleRequest($request);
            $passwordForm->handleRequest($request);
            
            if($this->handleForm($emailForm, $passwordForm)) {
                return $this->redirectToRoute('_user', array('userId' => $user->getId()));
            }
        }

        return $this->render(
            'AppBundle:Twig:user.html.twig',
            array(
             'title'        => 'sandbox|user profile',
             'recent'       => $recent,
             'user'         => $user,
             'passwordForm' => $passwordForm->createView(),
             'emailForm'    => $emailForm->createView()
            )
        );

    }

    public function registrationAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() === true) {
            $confirmationTokenManager = new ConfirmationTokenGenerator();
            $user->setConfirmationToken($confirmationTokenManager->generateConfirmationToken());

            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPlainPassword(null);
            $user->setPassword($password);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            #placeholder for message
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('Registration submitted, please check Your email and finish registration progress.');

            return $this->redirectToRoute('_home');
        }

        return $this->render(
            'AppBundle:Twig:registration.html.twig',
            array(
             'title'   => 'sandbox|project',
             'form'    => $form->createView()
            )
        );

    }

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

        }
        return $this->redirectToRoute('_home');

    }

    public function passwordResetRequestAction(Request $request)
    {
        $form = $this->createFormBuilder()
                     ->add('username', TextType::class, array('label' => 'Your username: ', 'required' => true))
                     ->add('reset password', SubmitType::class)
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
            }
            return $this->redirectToRoute('_home');
        }
        return $this->render(
            'AppBundle:Twig:reset.html.twig',
            array(
             'title'       => 'sandbox|project',
             'requestForm' => $form->createView(),
            )
        );

    }


    public function passwordResetVerificationAction(Request $request, $confirmationToken)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user          = $entityManager->getRepository('AppBundle:User')->findOneBy(['confirmationToken' => $confirmationToken]);
        $flash         = $this->get('braincrafted_bootstrap.flash');

        if ($user === false) {
            $flash->error('Oops, no user with matching token found!');
            return $this->redirectToRoute('_home');
        }

        $form = $this->createForm(VerificationFormType::class);
        $form->handleRequest($request);

        if ($form->isValid() === true) {
            $data = $form->getData();
            $user->setPlainPassword($data['plainPassword']);
            $user->setConfirmationToken(null);
            $user->setUpdated(new \Datetime());
            $entityManager->flush();
            $flash->success('Password for '.$user->getUsername().' was changed successfully!');
            return $this->redirectToRoute('_user', array('userId' => $user->getId()));
        }

        return $this->render(
            'AppBundle:Twig:reset.html.twig',
            array(
             'title'      => 'sandbox|project',
             'submitForm' => $form->createView(),
            )
        );

    }


    private function handleForm($emailForm, $passwordForm)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $flash         = $this->get('braincrafted_bootstrap.flash');
        $user          = $this->getUser();

        if($emailForm->isValid() === true){
            $data = $emailForm->getData();
            $user->setEmail($data['email']);
            $user->setUpdated(new \Datetime());
            $flash->success('User '.$user->getUsername().' email was changed successfully!');
            $entityManager->flush();
            return true;
        } else {
            $error = (string) $emailForm->getErrors(true);
            if (empty($error) === false) {
                $flash->error($error);
                return true;
            }
        }


        if($passwordForm->isValid() === true) {
            $data = $passwordForm->getData();
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $data['plainPassword']);
            $user->setPassword($password);
            $user->setUpdated(new \Datetime());
            $flash->success('User '.$user->getUsername().' password was changed successfully!');
            $entityManager->flush();
            return true;
        } else {
            $error = (string) $passwordForm->getErrors(true);
            if (empty($error) === false) {
                $flash->error($error);
                return true;
            }
        }

        return false;
    }

    public function sendVerificationAction()
    {
        $user = $this->getUser();
        $this->sendVerificationEmail($user);
        $flash = $this->get('braincrafted_bootstrap.flash');
        $flash->success('Email containing Verification Link has been successfully sent to' . $user->getEmail());
        return $this->redirectToRoute('_home');   
    }
    private function sendVerificationEmail($user)
    {
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
    }

}
