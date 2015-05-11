<?php

namespace AppBundle\Doctrine;

use AppBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserListener
{
	private $encoder;
	private $tokenStorage;
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if($entity instanceof User){
			$this->handleEvent($entity);
		}
	}
	public function preUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if($entity instanceof User){
			$entity->setUpdated(new \Datetime());
			$this->handleEvent($entity);
		}
	}
	public function postUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if($entity instanceof User){
			$this->authenticateUser($entity);
		}
	}
	public function postPersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if($entity instanceof User){
			$this->authenticateUser($entity);
		}
	}
	public function __construct(UserPasswordEncoder $encoder, TokenStorage $tokenStorage)
	{
		$this->encoder = $encoder;
		$this->tokenStorage = $tokenStorage;
	}
	private function handleEvent(User $user)
	{
		if(!$user->getPlainPassword()){
			return;
		}
		$encoded = $this->encoder->encodePassword($user, $user->getPlainPassword());
		$user->setPassword($encoded);
	}
	private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area';
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->tokenStorage->setToken($token);
    }    
}
