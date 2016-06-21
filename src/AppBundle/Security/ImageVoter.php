<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\Image;

class ImageVoter extends Voter
{
    const CREATE = 'create';
    const EDIT   = 'edit';
    const DELETE = 'delete';
    const VOTE   = 'vote';


    public function supports($attribute, $subject)
    {
        return $subject instanceof Image && in_array($attribute, array(
            self::CREATE, 
            self::EDIT, 
            self::DELETE, 
            self::VOTE)
        );
    }


    public function voteOnAttribute($attribute, $image, TokenInterface $token)
    {
        $user = $token->getUser();
        if ($user instanceof UserInterface === false) {
            return false;
        }

        switch($attribute) {
            case self::CREATE:
                return $user->getEnabled() || in_array('ROLE_ADMIN', $user->getRoles());

            case self::EDIT:
                return $user->getEnabled() && $user === $image->getOwner() || in_array('ROLE_ADMIN', $user->getRoles());

            case self::DELETE:
                return $user->getEnabled() && $user === $image->getOwner() || in_array('ROLE_ADMIN', $user->getRoles());

            case self::VOTE: 
                return $user->getEnabled() && $image->getOwner() !== $user;

            default:
                return false;
        }
    }


}
