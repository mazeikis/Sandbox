<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Image;
use AppBundle\Entity\User;

class LoadTestData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUserName('User');
        $user->setPlainPassword('test');
        $user->setFirstName('Jon');
        $user->setLastName('Doe');
        $user->setEmail('jon.doe@test.com');

        $manager->persist($user);

        $image = new Image();
        $image->setPath('/test/path/to/image/');
        $image->setsize(12345);
        $image->setTitle('Test Image Title 1');
        $image->setDescription('Test Image 1 description');
        $image->setResolution('test X test');
        $image->setOwner($user);

        $manager->persist($image);
        $manager->flush();

    }
}
