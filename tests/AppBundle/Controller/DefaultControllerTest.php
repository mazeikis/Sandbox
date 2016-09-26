<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 25/09/2016
 * Time: 22:53
 */

namespace Tests\AppBundle\Controller;


use Tests\AppBundle\FixturesAwareWebTestCase;

class DefaultControllerTest extends FixturesAwareWebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
    public function testAboutAction()
    {
        $client = static::createClient();

        $client->request('GET', '/about/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
    public function testApiDemoAction()
    {
        $client = static::createClient();

        $client->request('GET', '/rest/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
}