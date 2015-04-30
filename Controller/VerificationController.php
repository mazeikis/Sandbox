<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AppBundle\Entity\User;
use AppBundle\Security\TokenGenerator;

class VerificationController extends Controller
{
	public function emailAction(Request $request, $token)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$user = $em->getRepository('AppBundle:User')->findOneBy(['token' => $token]);
		if(!$user){
			$request->getSession()
	                ->getFlashBag()
	                ->add('error', 'Oops, no matching token found!');
	                return $this->redirectToRoute('_gallery');
		}else{
			$user->setEnabled(true)->setToken(NULL);
			$em->flush();
			$request->getSession()
	                ->getFlashBag()
	                ->add('success', 'User '.$user->getUsername().' verified successfuly!');
		           	return $this->redirectToRoute('_gallery');
	        }
    }
    public function resetAction(Request $request)
    {
    	$tempData = array();
    	$form = $this->createFormBuilder($tempData)
    	->add('username', 'text', array('label'  => 'Your username: ', 'required' => true))
		->add('save', 'submit', array('label' => 'Register'))
		->getForm();

		$form->handleRequest($request);
		if($form->isValid()){
			$em = $this->getDoctrine()->getEntityManager();
			$data = $form->getData();
			$user = $em->getRepository('AppBundle:User')->findOneBy(['username' => $data['username']]);

		    $tokenGenerator = new TokenGenerator();
			$user->setToken($tokenGenerator->generateToken());
	        $em->persist($user);
	        $em->flush();
	        $message = \Swift_Message::newInstance()
	        ->setSubject('Password reset Email')
	        ->setFrom('f.mazeikis@gmail.com')
	        ->setTo($user->getEmail())
	        ->setBody($this->renderView('AppBundle:Email:reset.txt.twig', array('link' => $user->getToken())));
            $this->get('mailer')->send($message);
	        $request->getSession()
		                ->getFlashBag()
		                ->add('success', 'User '.$user->getUsername().' reset email sent successfuly!');
			return $this->redirectToRoute('_home');
			}
		return $this->render('AppBundle:Default:registration.html.twig', array('title' => 'sandbox|project', 'form' => $form->createView()));
    }
    public function verifyResetAction(Request $request, $token)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$user = $em->getRepository('AppBundle:User')->findOneBy(['token' => $token]);
    	if(!$user){
			$request->getSession()
	                ->getFlashBag()
	                ->add('error', 'Oops, no matching user found!');
	                return $this->redirectToRoute('_home');
		}
		$tempData = array();
		$form = $this->createFormBuilder($tempData)
		->add('plainPassword', 'repeated', array(
	           'first_name'  => 'password',
	           'second_name' => 'confirm',
	           'type'        => 'password'))
		->add('save', 'submit', array('label' => 'Upload'))
		->getForm();

        $form->handleRequest($request);
        if($form->isValid()){
        	$data = $form->getData();
        	$user->setPlainPassword($data['plainPassword']);
        	$encoder = $this->container->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($encoded);
			$user->setToken(null);
			$em->flush();
			$request->getSession()
	                ->getFlashBag()
	                ->add('success', 'Users '.$user->getUsername().' password changed successfuly!');
	        $this->authenticateUser($user);
		    return $this->redirectToRoute('_user', array('slug' => $user->getId()));
	        }
    		return $this->render('AppBundle:Default:registration.html.twig', array('title' => 'sandbox|project', 'form' => $form->createView()));
    }
    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area'; // your firewall name
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->container->get('security.context')->setToken($token);
    }
}