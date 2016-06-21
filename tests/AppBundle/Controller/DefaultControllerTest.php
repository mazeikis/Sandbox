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
        $parameters = array();

        //User page wen user is not logged in
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        //User page when user logged in
        $client->request($method, $uri, $parameters, array(), array('PHP_AUTH_USER' => 'TestUsername1',
            'PHP_AUTH_PW'   => 'TestPassword1'));
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
}
