<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use AppBundle\Entity\Vote;

class LoadTestData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
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
        $encoder  = $this->container->get('security.password_encoder');

        $user = new User();
        $user->setId(1);
        $user->setUserName('TestUsername1');
        $password = $encoder->encodePassword($user, 'TestPassword1');
        $user->setPassword($password);
        $user->setFirstName('Jon');
        $user->setLastName('Doe');
        $user->setRoles('ROLE_USER');
        $user->setEnabled(true);
        $user->setApiKey('TestApiKey1');
        $user->setEmail('jon.doe@test.com');
        $this->addReference('user-one', $user);

        $manager->persist($user);

        $user = new User();
        $user->setId(2);
        $user->setUserName('TestUsername2');
        $password = $encoder->encodePassword($user, 'TestPassword2');
        $user->setPassword($password);
        $user->setFirstName('Jon');
        $user->setLastName('Doe');
        $user->setRoles('ROLE_USER');
        $user->setEnabled(true);
        $user->setApiKey('TestApiKey2');
        $user->setEmail('jon.doe2@test.com');
        $this->addReference('user-two', $user);


        $manager->persist($user);

        $user = new User();
        $user->setId(3);
        $user->setUserName('TestUsername3');
        $password = $encoder->encodePassword($user, 'TestPassword3');
        $user->setPassword($password);
        $user->setFirstName('Jon');
        $user->setLastName('Doe');
        $user->setRoles('ROLE_USER');
        $user->setEnabled(false);
        $user->setConfirmationToken('TestConfirmationToken1');
        $user->setApiKey('TestApiKey3');
        $user->setEmail('jon.doe3@test.com');

        $manager->persist($user);

        $user = new User();
        $user->setId(4);
        $user->setUserName('TestUsername4');
        $password = $encoder->encodePassword($user, 'TestPassword4');
        $user->setPassword($password);
        $user->setFirstName('Jon');
        $user->setLastName('Doe');
        $user->setRoles('ROLE_USER');
        $user->setEnabled(True);
        $user->setConfirmationToken('TestConfirmationToken2');
        $user->setApiKey('TestApiKey4');
        $user->setEmail('jon.doe4@test.com');

        $manager->persist($user);

        $image = new Image();
        $image->setId(1);
        $image->setFile('/test/path/to/image/1');
        $image->setSize(12345);
        $image->setTitle('Test Image Title 1');
        $image->setDescription('Test Image 1 description');
        $image->setResolution('test X test');
        $image->setCreated(new \DateTime());
        $image->setUpdated(new \DateTime());
        $image->setOwner($this->getReference('user-one'));
        $this->setReference('image-one', $image);

        $manager->persist($image);

        $image = new Image();
        $image->setId(2);
        $image->setFile('/test/path/to/image/2');
        $image->setSize(12345);
        $image->setTitle('Test Image Title 2');
        $image->setDescription('Test Image 2 description with query');
        $image->setResolution('test X test');
        $image->setCreated(new \DateTime());
        $image->setUpdated(new \DateTime());
        $image->setOwner($this->getReference('user-one'));

        $manager->persist($image);

        $image = new Image();
        $image->setId(3);
        $image->setFile('/test/path/to/image/3');
        $image->setSize(12345);
        $image->setTitle('Test Image Title 3');
        $image->setDescription('Test Image 3 description with query');
        $image->setResolution('test X test');
        $image->setCreated(new \DateTime());
        $image->setUpdated(new \DateTime());
        $image->setOwner($this->getReference('user-one'));

        $manager->persist($image);

        $vote = new Vote();
        $vote->setUser($this->getReference('user-two'));
        $vote->setImage($this->getReference('image-one'));
        $vote->setVote(1);

        $manager->persist($vote);


        $manager->flush();

    }
    protected function getEnvironments()
    {
        return ['test'];
    }
}
