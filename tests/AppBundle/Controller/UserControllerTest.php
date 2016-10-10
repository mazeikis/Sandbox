<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\FixturesAwareWebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
/**
 * Class UserControllerTest
 * @package Tests\AppBundle\Controller
 */
Class UserControllerTest extends FixturesAwareWebTestCase
{
    public function setUp()
    {
        $loader   = new Loader();
        $purger   = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);

        $testData = new LoadUserData();
        $testData->setContainer($this->container);
        $loader->addFixture($testData);

        $executor->execute($loader->getFixtures());
    }

    /**
     * @dataProvider testIndexActionProvider
     */
    public function testIndexAction($uri, $httpStatusCode, $username, $password)
    {
        $this->client->request('GET', $uri, array(), array(), array('PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW'   => $password));
        $response = $this->client->getResponse();
        $this->assertEquals($httpStatusCode, $response->getStatusCode());
    }

    /**
     * @return array
     */
    public function testIndexActionProvider()
    {
        return [
            'No user 401' => ['/user/1', 401, null, null],
            'Username with wrong password 403' => ['/user/1', 403, 'TestUsername1', 'xoxoxoxo'],
            'Username with correct password 200' => ['/user/1', 200, 'TestUsername1', 'TestPassword1'],
            'Not enabled user with password 200' => ['/user/3', 200, 'TestUsername3', 'TestPassword3']
        ];
    }

    /**
     * @dataProvider  testVerifyUserActionProvider
     */
    public function testVerifyUserAction($httpStatusCode, $confirmationToken)
    {
        $this->client->request('GET', '/verify/'.$confirmationToken);
        $response = $this->client->getResponse();
        $this->assertEquals($httpStatusCode, $response->getStatusCode());
    }

    /**
     * @return array
     */
    public function testVerifyUserActionProvider()
    {
        return [
            'No token 302'      => [404, null],
            'Fake token 302'    => [302, 'WrongToken'],
            'Correct token 302' => [302, 'TestConfirmationToken1']
        ];
    }

    /**
     * @dataProvider registrationActionProvider()
     */
    public function testRegistrationAction($method, $uri, $httpStatusCode)
    {
        $this->client->request($method, $uri);
        $response = $this->client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());

    }
    public function registrationActionProvider()
    {
        return [
            'Placeholder data' => ['GET', '/registration/', 200]
        ];
    }
}
