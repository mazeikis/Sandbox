<?php

namespace AppBundle\Controller\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


Class ApiControllerTest extends WebTestCase
{
	public function testDefaultAction()
    {
        $method 	= 'GET';
        $uri 		= '/api';
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
        $files 		= array();
        $server 	= array();
        $content 	= array();

        $client = static::createClient();
        $client->request($method, $uri, $parameters, $files, $server, $content);
        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));
    }
}
