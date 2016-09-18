<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\FixturesAwareWebTestCase;

Class GalleryControllerTest extends FixturesAwareWebTestCase
{
    public function testIndexAction()
    {
        $method = 'GET';
        $uri    = '/gallery/';
        $parameters = array();


        $client = static::createClient();

        $client->request($method, $uri);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $parameters = array('page' => -1);
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());


        $parameters = array('page' => 'xoxo');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());


        $parameters = array('page' => 1);
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());


        $parameters = array('sortBy' => 'xoxo');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());


        $parameters = array('order' => 'xoxo');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());


    }

    public function testImageAction(){
        $method = 'GET';
        $uri    = '/gallery/image/';


        $client = static::createClient();

        $client->request($method, $uri.'1');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $client->request($method, $uri.'111');
        $response = $client->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
    }
}
