<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 25/09/2016
 * Time: 22:53
 */

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = static::createClient();
    }

    public function testIndexAction()
    {

        $this->client->request('GET', '/');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
    public function testAboutAction()
    {
        $this->client->request('GET', '/about/');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
    public function testApiDemoAction()
    {
        $this->client->request('GET', '/rest/');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
}