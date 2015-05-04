<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AppBundle\Entity\User;
use AppBundle\Security\ConfirmationTokenGenerator;
use AppBundle\Form\RegistrationFormType;


class RegistrationController extends Controller
{
    public function indexAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new RegistrationFormType(), $user);
		$form->handleRequest($request);

        if($form->isValid()){
            $encoder = $this->container->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($encoded);

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
                ->add('success', 'Rergistration almost complete, please check Your email for verification link to finish registration process!');

             $this->authenticateUser($user);

            return $this->redirectToRoute('_home');
        }else{
            $message = NULL;
        }

        return $this->render('AppBundle:Twig:registration.html.twig', array('title' => 'sandbox|project', 'message' => $message, 'form' => $form->createView()));
    }
    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area';
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);
    }
}