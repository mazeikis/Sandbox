<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUserName('User');
        $user->setPlainPassword('test');
        $user->setId(19);

        $manager->persist($user);
        $manager->flush();

    }
}
