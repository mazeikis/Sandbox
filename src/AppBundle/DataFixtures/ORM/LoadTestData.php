<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Image;
use AppBundle\Entity\User;

class LoadTestData implements FixtureInterface, ContainerAwareInterface
{
    private $container;
    /**
    * @var ContainerInterface
    */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setId(1);
        $user->setUserName('TestUsername1');
        $encoder  = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user, 'TestPassword1');
        $user->setPassword($password);
        $user->setFirstName('Jon');
        $user->setLastName('Doe');
        $user->setRoles('ROLE_USER');
        $user->setEnabled(true);
        $user->setApiKey('TestApiKey1');
        $user->setEmail('jon.doe@test.com');

        $manager->persist($user);

        $image = new Image();
        $image->setId(1);
        $image->setPath('/test/path/to/image/');
        $image->setSize(12345);
        $image->setTitle('Test Image Title 1');
        $image->setDescription('Test Image 1 description');
        $image->setResolution('test X test');
        $image->setCreated(new \DateTime());
        $image->setUpdated(new \DateTime());
        $image->setOwner($user);


        $manager->persist($image);

        $user = new User();
        $user->setId(2);
        $user->setUserName('TestUsername2');
        $encoder  = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user, 'TestPassword2');
        $user->setPassword($password);
        $user->setFirstName('Jon');
        $user->setLastName('Doe');
        $user->setRoles('ROLE_USER');
        $user->setEnabled(true);
        $user->setApiKey('TestApiKey2');
        $user->setEmail('jon.doe2@test.com');

        $manager->persist($user);

        $manager->flush();

    }
}
