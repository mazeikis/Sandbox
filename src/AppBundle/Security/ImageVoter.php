<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

class ImageVoter extends AbstractVoter
{
    const CREATE = 'create';
    const EDIT   = 'edit';
    const DELETE = 'delete';
    const VOTE   = 'vote';


    protected function getSupportedAttributes()
    {
         return array(
                 self::CREATE,
                 self::EDIT,
                 self::DELETE,
                 self::VOTE,
                );

    }


    protected function getSupportedClasses()
    {
        return array(
                'AppBundle\Entity\Image',
                'AppBundle\Entity\User',
               );

    }


    protected function isGranted($attribute, $image=null, $user=null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if ($user instanceof UserInterface === false) {
            return false;
        }

        // logic to decide if the given user can create
        // edit and/or delete the given image
        switch($attribute) {
            case self::CREATE :
                return $user->getEnabled() || $user->getRoles() == 'ROLE_ADMIN';

            case self::EDIT :
                return $user->getEnabled() && $user == $image->getOwner() || $user->getRoles() == 'ROLE_ADMIN';

            case self::DELETE :
                return $user->getEnabled() && $user == $image->getOwner() || $user->getRoles() == 'ROLE_ADMIN';

            case self::VOTE : 
                return $user->getEnabled() && $image->getOwner() !== $user;

            default:
                return false;
        }
    }


}
