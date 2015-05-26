<?php

namespace AppBundle\Doctrine;

use AppBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserListener
{

    public $encoder;

    public $tokenStorage;


    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $this->handleEvent($entity);
        }//end if

    }//end prePersist()


    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
             $entity->setUpdated(new \Datetime());
             $this->handleEvent($entity);
        }//end if

    }//end preUpdate()


    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
             $this->authenticateUser($entity);
        }//end if

    }//end postUpdate()


    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $this->authenticateUser($entity);
        }//end if

    }//end postPersist()


    public function __construct(UserPasswordEncoder $encoder, TokenStorage $tokenStorage)
    {
        $this->encoder      = $encoder;
        $this->tokenStorage = $tokenStorage;

    }//end __construct()


    private function handleEvent(User $user)
    {
        if ($user->getPlainPassword() === false) {
            return;
        }

         $encoded = $this->encoder->encodePassword($user, $user->getPlainPassword());
         $user->setPassword($encoded);

    }//end handleEvent()


    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area';
        $token       = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->tokenStorage->setToken($token);

    }//end authenticateUser()


}//end class
