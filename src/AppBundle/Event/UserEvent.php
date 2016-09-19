<?php
/**
 * Created by PhpStorm.
 * User: Feliksas
 * Date: 18/09/2016
 * Time: 17:28
 */

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class UserCreatedEvent
 * @package AppBundle\Event
 */
class UserEvent extends Event
{
    const USER_CREATED_EVENT = 'user.created.event';
    const USER_VERIFICATION_EVENT = 'user.verification.event';

    private $user;
    private $session;

    /**
     * @param User $user
     * @param SessionInterface $session
     */
    public function __construct(User $user, SessionInterface $session) {
        $this->user    = $user;
        $this->session = $session;
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }
}
