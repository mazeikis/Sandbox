<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 22/09/2016
 * Time: 12:20
 */

namespace Tests\AppBundle\Security;


use AppBundle\Entity\Image;
use AppBundle\Security\ImageVoter;
use Tests\AppBundle\FixturesAwareWebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class ImageVoterTest
 * @package Tests\AppBundle\Security
 */
class ImageVoterTest extends FixturesAwareWebTestCase
{

    /**
     * @dataProvider testSupportsProvider
     */
    public function testSupports($attribute)
    {
        $image = $this->em->getRepository('AppBundle:Image')->findOneBy(array('id' => 1));
        $voter = new ImageVoter();
        $this->assertTrue($voter->supports($attribute, $image));
    }

    /**
     * @return array
     */
    public function testSupportsProvider()
    {
        return [
            'create' => array('create'),
            'edit'   => array('edit'),
            'delete' => array('delete'),
            'vote'   => array('vote')
        ];
    }

    /**
     *
     */
    public function testVoteOnAttribute()
    {
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('id' => 1));
        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $voter = new ImageVoter();

        $image = new Image();

        //Authorized user creating image
        $result = $voter->voteOnAttribute('create', $image, $token);
        $this->assertTrue($result);


        //Image owner editing image
        $image = $this->em->getRepository('AppBundle:Image')->findOneBy(array('id' => 1));
        $result = $voter->voteOnAttribute('edit', $image, $token);
        $this->assertTrue($result);

        //Unauthorized user editing image
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('id' => 3));
        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());

        $result = $voter->voteOnAttribute('edit', $image, $token);
        $this->assertFalse($result);

    }
}
