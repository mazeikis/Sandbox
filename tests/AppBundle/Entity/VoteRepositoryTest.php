<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 22/09/2016
 * Time: 12:06
 */

namespace Tests\AppBundle\Entity;
use Tests\AppBundle\FixturesAwareWebTestCase;
use AppBundle\Entity\Vote;

/**
 * Class VoteRepositoryTest
 * @package Tests\AppBundle\Entity
 */
class VoteRepositoryTest extends FixturesAwareWebTestCase
{

    /**
     *
     */
    public function testCheckForVote()
    {
        $image = $this->em->getRepository('AppBundle:Image')->findOneBy(array('id' => 1));
        $user  = $this->em->getRepository('AppBundle:User')->findOneBy(array('id' => 2));
        $result = $this->em->getRepository('AppBundle:Vote')->checkForVote($user, $image);
        $this->assertTrue($result instanceof Vote);

    }
}
