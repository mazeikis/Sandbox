<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AppBundle\Entity\User;
use AppBundle\Security\confirmationTokenGenerator;
use AppBundle\Form\Type\VerificationFormType;

class VerificationController extends Controller
{
	public function emailAction(Request $request, $confirmationToken)
	{
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('AppBundle:User')->findOneBy(['confirmationToken' => $confirmationToken]);
		if(!$user){
			$request->getSession()
	                ->getFlashBag()
	                ->add('success', 'Oops, no matching token found!');
	                return $this->redirectToRoute('_home');
		}else{
			$user->setEnabled(true)->setConfirmationToken(NULL);
			$em->flush();
			$request->getSession()
	                ->getFlashBag()
	                ->add('success', 'User '.$user->getUsername().' verified successfuly!');
		           	return $this->redirectToRoute('_home');
	        }
    }
    public function resetAction(Request $request)
    {
    	$tempData = array();
    	$form = $this->createFormBuilder($tempData)
    	    ->add('username', 'text', array('label'  => 'Your username: ', 'required' => true))
		    ->add('reset password', 'submit')
		    ->getForm();

		$form->handleRequest($request);
		if($form->isValid()){
			$em = $this->getDoctrine()->getManager();
			$data = $form->getData();
			$user = $em->getRepository('AppBundle:User')->findOneBy(['username' => $data['username']]);

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
			}
		return $this->render('AppBundle:Twig:reset.html.twig', array('title' => 'sandbox|project', 'requestForm' => $form->createView()));
    }
    public function verifyResetAction(Request $request, $confirmationToken)
    {
    	$em = $this->getDoctrine()->getManager();
    	$user = $em->getRepository('AppBundle:User')->findOneBy(['confirmationToken' => $confirmationToken]);
    	if(!$user){
			$request->getSession()
	                ->getFlashBag()
	                ->add('success', 'Oops, no matching user found!');
	                return $this->redirectToRoute('_home');
		}
		$form = $this->createForm(new VerificationFormType());
        $form->handleRequest($request);
        if($form->isValid()){
        	$data = $form->getData();
        	$user->setPlainPassword($data['plainPassword']);
        	$encoder = $this->container->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($encoded);
			$user->setConfirmationToken(null);
			$em->flush();
			$request->getSession()
	                ->getFlashBag()
	                ->add('success', 'Users '.$user->getUsername().' password changed successfuly!');
	        $this->authenticateUser($user);
		    return $this->redirectToRoute('_user', array('slug' => $user->getId()));
	        }
    		return $this->render('AppBundle:Twig:reset.html.twig', array('title' => 'sandbox|project', 'submitForm' => $form->createView()));
    }
    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area'; // your firewall name
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);
    }
}
