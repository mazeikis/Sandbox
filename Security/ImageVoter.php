<?php
namespace Sandbox\Bundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

class ImageVoter extends AbstractVoter
{
    const CREATE = 'create';
    const EDIT = 'edit';
    const DELETE = 'delete';
    
    protected function getSupportedAttributes()
    {
    	 return array(
            self::CREATE,
            self::EDIT,
            self::DELETE
        );
    }

 	protected function getSupportedClasses()
    {
        return array(
        	'Sandbox\Bundle\Entity\Image',
        	'Sandbox\Bundle\Entity\User'
        	);
    }

    protected function isGranted($attribute, $image = null, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        // custom business logic to decide if the given user can view
        // and/or edit the given post
        switch($attribute) {
        	case 'create':
        		if($user->getEnabled() || $user->getRoles() == 'ROLE_ADMIN'){
        			return true;
        		}
        	case 'edit':
        		if($user->getEnabled() && $user == $image->getOwner() || $user->getRoles() == 'ROLE_ADMIN'){
        			return true;
        		}
        	case 'delete':
        		case 'edit':
        		if($user->getEnabled() && $user == $image->getOwner() || $user->getRoles() == 'ROLE_ADMIN'){
        			return true;
        		}
        }
        return false;
    }
}