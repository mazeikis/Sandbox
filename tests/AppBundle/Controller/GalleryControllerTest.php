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
            'No parameters assert 200'                  => [null, null, 200],
            'Negative page assert 404'                  => ['page', -1, 404],
            'Page !integer assert 404'                  => ['page', 'xoxo', 404],
            'Page does not exist assert 404'            => ['page', 111, 404],
            'Page is integer and exists assert 200'     => ['page', 1, 200],
            'SortBy != created|title|rating assert 200' => ['sortBY', 'xoxo', 200],
            'Order != asc|desc assert 200'              => ['order', 'xoxo', 200]

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
            'Existing id assert 200'        => ['/gallery/image/1', 200],
            'Non existant id assert 404'    => ['/gallery/image/111', 404],
            'Incorret format id assert 404' => ['/gallery/image/xoxo', 404]
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
            'Correct id, authorized user 302'    => ['/gallery/image/edit/1', 'TestUsername1', 'TestPassword1', 302],
            'Bad format id, authorized user 404' => ['/gallery/image/edit/xoxo', 'TestUsername1', 'TestPassword1', 404],
            'Coorect id, no user 302'            => ['/gallery/image/edit/1', null, null, 302],
        ];
    }
}
