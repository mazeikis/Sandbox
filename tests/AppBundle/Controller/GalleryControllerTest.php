<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\FixturesAwareWebTestCase;

Class GalleryControllerTest extends FixturesAwareWebTestCase
{
    /**
     * @dataProvider indexActionProvider
     */
    public function testIndexAction($key, $value, $httpStatusCode)
    {
        $client = static::createClient();

        $client->request('GET', '/gallery/', array($key => $value));
        $response = $client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());

    }
    public function indexActionProvider(){
        return [
            'No parameters'                  => [null, null, 200],
            'Negative page'                  => ['page', -1, 404],
            'Page !integer'                  => ['page', 'xoxo', 404],
            'Page does not exist'            => ['page', 111, 404],
            'Page is integer and exists'     => ['page', 1, 200],
            'SortBy != created|title|rating' => ['sortBY', 'xoxo', 200],
            'Order != asc|desc'              => ['order', 'xoxo', 200]

        ];
    }

    /**
     * @dataProvider imageActionProvider
     */
    public function testImageAction($uri, $httpStatusCode){

        $client = static::createClient();

        $client->request('GET', $uri);
        $response = $client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());
    }
    public function imageActionProvider(){
        return [
            'Existing id'        => ['/gallery/image/1', 200],
            'Non existant id'    => ['/gallery/image/111', 404],
            'Incorret format id' => ['/gallery/image/xoxo', 404]
        ];
    }

    /**
     * @dataProvider imageEditActionProvider
     */
    public function testImageEditAction($uri, $username, $password, $httpStatusCode){
        $client = static::createClient();

        $client->request('POST', $uri, array(), array(), array('PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW'   => $password));
        $response = $client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());
    }
    public function imageEditActionProvider(){
        return [
            'Correct id, authorized user' => ['/gallery/image/edit/1', 'TestUsername1', 'TestPassword1', 302]
        ];
    }
}
