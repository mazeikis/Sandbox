<?php

namespace AppBundle\Doctrine;

use AppBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserListener
{
	private $encoder;
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
	public function __construct(UserPasswordEncoder $encoder)
	{
		$this->encoder = $encoder;
	}
	private function handleEvent(User $user)
	{
		if(!$user->getPlainPassword()){
			return;
		}
		$encoded = $this->encoder->encodePassword($user, $user->getPlainPassword());
		$user->setPassword($encoded);
	}
}
