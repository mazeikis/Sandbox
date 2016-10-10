<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 22/09/2016
 * Time: 10:49
 */
namespace Tests\AppBundle\Entity;


use Tests\AppBundle\FixturesAwareWebTestCase;

/**
 * Class ImageRepositoryTest
 * @package Tests\AppBundle\Entity
 */
class ImageRepositoryTest extends FixturesAwareWebTestCase
{
    /**
     *
     */
    public function testGetImages()
    {
        $images = $this->em->getRepository('AppBundle:Image')->getImages('created', 'desc')->getQuery()->getResult();
        $this->assertCount(3, $images);
        $images = $this->em->getRepository('AppBundle:Image')->getImages('created', 'desc', 'query')->getQuery()->getResult();
        $this->assertCount(2, $images);
    }
}
