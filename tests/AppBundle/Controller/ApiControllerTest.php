<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\FixturesAwareWebTestCase;

Class ApiControllerTest extends FixturesAwareWebTestCase
{

    public function testGalleryAction()
    {
    	$method = 'GET';
    	$uri 	= '/api/gallery/';
    	$parameters = array();


        $client = static::createClient();

        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
    	));

        $parameters = array('page' => -1);
        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        $parameters = array('page' => 'xoxo');
        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        $parameters = array('page' => 1);
        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        $parameters = array('sortBy' => 'xoxo');
        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        $parameters = array('order' => 'xoxo');
        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));


    }
    public function testImageAction()
    {
        $method = 'GET';
        $uri    = '/api/image/1';
        $client = static::createClient();

        //Correct and existing image id
        $client->request($method, $uri, array(), array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        //Incorrect and non existant image id
        $uri    = '/api/image/xoxo';
        $client->request($method, $uri, array(), array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        //Correct, but non existant image id
        $uri    = '/api/image/191919';
        $client->request($method, $uri, array(), array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));
    }
    public function testImageDeleteAction()
    {
        $method     = 'DELETE';
        $uri        = '/api/image/delete/1';
        $client     = static::createClient();

        //No user logged in
        $client->request($method, $uri);
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        //With authorised user logged in, but no 'delete' rights
        $client->request($method, $uri, array(), array(), array('HTTP_X-Token' => 'TestApiKey2'));
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        //With authorised user logged in
        $client->request($method, $uri, array(), array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        //Non existing and incorrect format of id with value of 'xoxo'
        $uri    = '/api/image/delete/xoxo';
        $client->request($method, $uri, array(), array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

    }
    public function testImageVoteAction()
    {
        $method = 'POST';
        $uri    = '/api/image/vote/';
        $client = static::createClient();

        //Wrong value
        $parameters = array('id' => 1, 'voteValue' => 'xoxo');
        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey2'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        //Non existing id
        $parameters = array('id' => 12345, 'voteValue' => '1');
        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey2'));
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        //Existing id and correct value, but no authorised user.
        $parameters = array('id' => 1, 'voteValue' => '1');
        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));
        
        //With user logged in, but has no rights to vote
        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey1'));
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));

        //With authorised user logged in, that has rights to vote
        $client->request($method, $uri, $parameters, array(), array('HTTP_X-Token' => 'TestApiKey2'));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));
    }
}
