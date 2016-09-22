<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\FixturesAwareWebTestCase;

/**
 * Class ApiControllerTest
 * @package Tests\AppBundle\Controller
 */
Class ApiControllerTest extends FixturesAwareWebTestCase
{
    /**
     * @dataProvider galleryActionProvider
     */
    public function testGalleryAction($key, $value, $httpStatusCode = 400)
    {
        $client = static::createClient();

        $client->request('GET', '/api/gallery/', array($key => $value));
        $response = $client->getResponse();

        $this->assertEquals($httpStatusCode, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
    	));

    }

    /**
     * @return array
     */
    public function galleryActionProvider(){
        return [
            'No parameter      assert 200' => [null, null, 200],
            'Negative integer  assert 400' => ['page', -1, 400],
            'String page       assert 404' => ['page', 'xoxo', 404],
            'Positive integer  assert 200' => ['page', 1, 200],
            'Sort by "xoxo"    assert 400' => ['sortBy', 'xoxo', 400],
            'Sort by "created" assert 200' => ['sortBy', 'created', 200],
            'Sort by "title"   assert 200' => ['sortBy', 'title', 200],
            'Sort by "rating"  assert 200' => ['sortBy', 'rating', 200],
            'Order by "xoxo"   assert 400' => ['order', 'xoxo', 400],
            'Order by "ASC"    assert 200' => ['order', 'asc', 200],
            'Order by "DESC"   assert 200' => ['order', 'desc', 200]
        ];
    }

    /**
     * @dataProvider imageActionProvider
     */
    public function testImageAction($uri, $httpStatuCode)
    {
        $client = static::createClient();

        //Correct and existing image id
        $client->request('GET', $uri);
        $response = $client->getResponse();
        $this->assertEquals($httpStatuCode, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));
    }

    /**
     * @return array
     */
    public function imageActionProvider(){
        return [
            'Correct uri     200' => ['/api/image/1', 200],
            'Invalid format  400' => ['/api/image/xoxo', 404],
            'Non existant id 404' => ['/api/image/111', 404]
        ];
    }

    /**
     * @param $uri
     * @param $tokenKey
     * @param $tokenValue
     * @param $httpStatusCode
     * @dataProvider imageDeleteActionProvider
     */
    public function testImageDeleteAction($uri, $tokenValue, $httpStatusCode)
    {
        $client     = static::createClient();

        $client->request('DELETE', $uri, array(), array(), array('HTTP_X-Token' => $tokenValue));
        $response = $client->getResponse();
        $this->assertEquals($httpStatusCode, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));
    }

    /**
     * @return array
     */
    public function imageDeleteActionProvider(){
        return [
            'Correct uri, no user'                   => ['/api/image/delete/1', null, 401],
            'Correct uri, user logged in, no rights' => ['/api/image/delete/1', 'TestApiKey2', 401],
            'Correct uri, authorized user'           => ['/api/image/delete/1', 'TestApiKey1', 200],
            'Incorrect uri, user logged in'          => ['/api/image/delete/xoxo', 'TestApiKey1', 404],
            'Non existing image, user logged in'     => ['/api/image/delete/111', 'TestApiKey1', 404],
        ];
    }

    /**
     * @param $id
     * @param $voteValue
     * @param $token
     * @dataProvider imageVoteActionProvider
     */
    public function testImageVoteAction($id, $voteValue, $token, $httpStatusCode)
    {
        $client = static::createClient();

        $client->request('POST',
                         '/api/image/vote/',
                         array('id' => $id, 'voteValue' => $voteValue),
                         array(),
                         array('HTTP_X-Token' => $token)
        );

        $response = $client->getResponse();
        $this->assertEquals($httpStatusCode, $response->getStatusCode());
        $this->assertTrue($response->headers->contains(
            'Content-Type',
            'application/json'
        ));
    }

    /**
     * @return array
     */
    public function imageVoteActionProvider(){
        return [
            'Correct id, incorrect vote format, no right to vote' => [1, 'xoxo', 'TestApiKey1', 401],
            'Non existant id, correct vote format, no right to vote' => [111, 1, 'TestApiKey1', 404],
            'Correct id and vote format, no user' => [1, 1, null, 401],
            'Correct id and vote format, no rights to vote' => [1, 1, 'TestApiKey1', 401],
            'Correct id and vote format, has right to vote' => [1, 1, 'TestApiKey4', 200]
        ];
    }
}
