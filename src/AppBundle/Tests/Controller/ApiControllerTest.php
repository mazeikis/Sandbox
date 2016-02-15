<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\FixturesAwareWebTestCase;

Class ApiControllerTest extends FixturesAwareWebTestCase
{

    public function testGalleryAction()
    {
    	$method = 'GET';
    	$uri 	= '/api/gallery/';
    	$parameters = array();


        $client = static::createClient();

        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));

        $parameters = array('page' => -1);
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

        $parameters = array('page' => 'xoxo');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

        $parameters = array('page' => 1);
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

        $parameters = array('sortBy' => 'xoxo');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

        $parameters = array('order' => 'xoxo');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));


    }
    public function testImageAction()
    {
        $method = 'GET';
        $uri    = '/api/image/1';
        $client = static::createClient();

        $client->request($method, $uri);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

        $uri    = '/api/image/xoxo';
        $client->request($method, $uri);
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

        $uri    = '/api/image/191919';
        $client->request($method, $uri);
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));
    }
    public function testImageDeleteAction()
    {
        $method = 'DELETE';
        $uri    = '/api/image/delete/1';
        $client = static::createClient();

        $client->request($method, $uri);
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

        $uri    = '/api/image/delete/xoxo';
        $client->request($method, $uri);
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

    }
    public function testImageVoteAction()
    {
        $method = 'POST';
        $uri    = '/api/image/vote/';
        $client = static::createClient();

        $parameters = array('id' => 1, 'voteValue' => 'xoxo');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

        $parameters = array('id' => 12345, 'voteValue' => '1');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

        $parameters = array('id' => 1, 'voteValue' => '1');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
        ));

    }
}
