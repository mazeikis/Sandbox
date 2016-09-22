<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\FixturesAwareWebTestCase;

Class DefaultControllerTest extends FixturesAwareWebTestCase
{
    /**
     * @dataProvider testIndexActionProvider
     */
    public function testIndexAction($httpStatusCode, $username, $password)
    {
        $client = static::createClient();

        $client->request('GET', '/user/1', array(), array(), array('PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW'   => $password));
        $response = $client->getResponse();
        $this->assertEquals($httpStatusCode, $response->getStatusCode());
    }
    public function testIndexActionProvider()
    {
        return [
            'No user assert 401' => [401, null, null],
            'Username with wrong password' => [403, 'TestUsername1', 'xoxoxoxo'],
            'Username with correct password' => [200, 'TestUsername1', 'TestPassword1'],
        ];
    }
}
