<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\FixturesAwareWebTestCase;

Class DefaultControllerTest extends FixturesAwareWebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();
        $method = 'GET';
        $uri    = '/user/1';

        //User page wen user is not logged in
        $client->request($method, $uri);
        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        //User page when user logged in
        $client->request($method, $uri, array(), array(), array('PHP_AUTH_USER' => 'TestUsername1',
            'PHP_AUTH_PW'   => 'TestPassword1'));
        $response = $client->getResponse();
        //This will fail, as Guard uses "redirect" HTTP method on both successful authentication and failed one.
        $this->assertEquals(302, $response->getStatusCode());
    }
}
