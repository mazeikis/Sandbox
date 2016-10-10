<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
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

        $manager->flush();

    }
    protected function getEnvironments()
    {
        return ['test'];
    }
}
