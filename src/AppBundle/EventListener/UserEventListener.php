<?php
/**
 * Created by PhpStorm.
 * User: Feliksas Mazeikis
 * Date: 18/09/2016
 * Time: 17:52
 */

namespace AppBundle\EventListener;


use AppBundle\Event\UserEvent;


/**
 * Class UserEventListener
 * @package AppBundle\EventListener
 */
class UserEventListener
{
    /**
     * @var
     */
    private $container;

    /**
     * UserEventListener constructor.
     * @param $container
     */
    public function __construct($container){
        $this->container = $container;
    }

    /**
     * Sends email with user verification link and adds success flash message.
     * @param UserEvent $event
     */
    public function onUserCreatedAction(UserEvent $event){
        $user    = $event->getUser();
        $session = $event->getSession();
        $session->getFlashBag()->add('success', 'Registration submitted, please check Your email and finish registration progress.');

        $this->sendEmail($user, 'AppBundle:Email:registration.txt.twig');

        return;
    }

    /**
     * Sends email with link to password reset and adds success flash message.
     * @param UserEvent $event
     */
    public function onUserVerificationAction(UserEvent $event){
        $user    = $event->getUser();
        $session = $event->getSession();

        $this->containersendEmail($user, 'AppBundle:Email:reset.txt.twig');

        $session->getFlashBag()->add('success', 'User '.$user->getUsername().' reset email sent successfuly!');

        return;
    }

    /**
     * Helper method for sending email, DRY.
     * @param $user
     * @param $template
     */
    private function sendEmail($user, $template){
        $message = \Swift_Message::newInstance()
            ->setContentType("text/html")
            ->setSubject('codeSandbox Email Robot')
            ->setFrom('robot@codesandbox.info')
            ->setTo($user->getEmail())
            ->setBody($this->container->get('twig')->render(
                $template,
                array('link' => $user->getConfirmationToken())
            ));
        $this->container->get('mailer')->send($message);
        return;
    }
}