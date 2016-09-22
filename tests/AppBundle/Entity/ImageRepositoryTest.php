<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 22/09/2016
 * Time: 10:49
 */
namespace Tests\AppBundle\Entity;


use Doctrine\ORM\EntityManager;
use Tests\AppBundle\FixturesAwareWebTestCase;

/**
 * Class ImageRepositoryTest
 * @package Tests\AppBundle\Entity
 */
class ImageRepositoryTest extends FixturesAwareWebTestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     *
     */
    public function testGetImages()
    {
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $images = $this->em->getRepository('AppBundle:Image')->getImages('created', 'desc')->getQuery()->getResult();
        $this->assertCount(1, $images);

    }
}
