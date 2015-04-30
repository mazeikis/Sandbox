<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AppBundle\Entity\User;
use AppBundle\Security\TokenGenerator;


class RegistrationController extends Controller
{
    public function indexAction(Request $request)
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
	        ->add('username', 'text', array('label'  => 'Choose username: ', 'required' => true))
			->add('plainPassword', 'repeated', array(
	           'first_name'  => 'password',
	           'second_name' => 'confirm',
	           'type'        => 'password'))
			->add('firstName','text', array('label'  => 'Your first name: ', 'required' => true))
			->add('lastName','text', array('label'  => 'Your last name: ', 'required' => true))
			->add('email', 'email', array('label'  => 'Your email address: ', 'required' => true))
			->add('save', 'submit', array('label' => 'Register'))
			->getForm();
		
		$form->handleRequest($request);

        if($form->isValid()){
            $encoder = $this->container->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($encoded);

            $tokenGenerator = new TokenGenerator();
            $user->setToken($tokenGenerator->generateToken());

        	$em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $message = \Swift_Message::newInstance()
            ->setSubject('Verification Email')
            ->setFrom('f.mazeikis@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                   'AppBundle:Email:email.txt.twig',
                    array('link' => $user->getToken())
                )
            );
            $this->get('mailer')->send($message);
            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Rergistration almost complete, please check Your email for verification link to finish registration process!');

             $this->authenticateUser($user);

            return $this->render('AppBundle:Default:gallery.html.twig', array('title' => 'sandbox|gallery'));
        }else{
            $message = NULL;
        }

        return $this->render('AppBundle:Default:registration.html.twig', array('title' => 'sandbox|project', 'message' => $message, 'form' => $form->createView()));
    }
    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area'; // your firewall name
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);
    }
}