<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\FixturesAwareWebTestCase;

/**
 * Class UserControllerTest
 * @package Tests\AppBundle\Controller
 */
Class UserControllerTest extends FixturesAwareWebTestCase
{
    /**
     * @dataProvider testIndexActionProvider
     */
    public function testIndexAction($uri, $httpStatusCode, $username, $password)
    {
        $client = static::createClient();

        $client->request('GET', $uri, array(), array(), array('PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW'   => $password));
        $response = $client->getResponse();
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
        $client = static::createClient();

        $client->request('GET', '/verify/'.$confirmationToken);
        $response = $client->getResponse();
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
        $client = static::createClient();

        $client->request($method, $uri);
        $response = $client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());

    }
    public function registrationActionProvider()
    {
        return [
            'Placeholder data' => ['GET', '/registration/', 200]
        ];
    }
}
