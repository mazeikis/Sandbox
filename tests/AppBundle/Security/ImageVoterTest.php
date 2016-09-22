<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 22/09/2016
 * Time: 12:20
 */

namespace Tests\AppBundle\Security;


use AppBundle\Security\ImageVoter;
use Doctrine\ORM\EntityManager;
use Tests\AppBundle\FixturesAwareWebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class ImageVoterTest
 * @package Tests\AppBundle\Security
 */
class ImageVoterTest extends FixturesAwareWebTestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @dataProvider testSupportsProvider
     */
    public function testSupports($attribute)
    {
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
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
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $image = $this->em->getRepository('AppBundle:Image')->findOneBy(array('id' => 1));
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('id' => 2));
        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $voter = new ImageVoter();
        $result = $voter->voteOnAttribute('create', $image, $token);

        $this->assertTrue($result);

    }
}
