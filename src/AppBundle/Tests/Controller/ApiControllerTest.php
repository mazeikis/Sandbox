<?php

namespace AppBundle\Controller\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\DataFixtures\ORM\LoadImageData;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

Class ApiControllerTest extends WebTestCase
{
    public function setUp()
    {
        $client = static::createClient();
        $kernel = $client->getContainer()->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array('command' => 'doctrine:fixtures:load', '-n'));
        $application->run($input);
    }

    public function testDefaultAction()
    {
        $method 	= 'GET';
        $uri 		= '/api/';
        $parameters = array();
        $files 		= array();
        $server 	= array();
        $content 	= array();

        $client = static::createClient();
        $client->request($method, $uri, $parameters, $files, $server, $content);
        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
    }
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

        $parameters = array('page' => 'xoxo');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());

        $parameters = array('page' => 1);
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $parameters = array('sortBy' => 'xoxo');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());

        $parameters = array('order' => 'xoxo');
        $client->request($method, $uri, $parameters);
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());


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
}
