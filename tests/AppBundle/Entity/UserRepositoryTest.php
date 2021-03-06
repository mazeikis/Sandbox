<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 22/09/2016
 * Time: 18:28
 */

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\User;
use Tests\AppBundle\FixturesAwareWebTestCase;

class UserRepositoryTest extends FixturesAwareWebTestCase
{

    /**
     *
     */
    public function testGetUser()
    {
        $user  = $this->em->getRepository('AppBundle:User')->loadUserByUsername('TestUsername1');
        $this->assertTrue($user instanceof User);
    }
}
