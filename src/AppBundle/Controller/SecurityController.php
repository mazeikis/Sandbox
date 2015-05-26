<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{


    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error               = $authenticationUtils->getLastAuthenticationError();
        $lastUsername        = $authenticationUtils->getLastUsername();
        return $this->redirectToRoute('_home', array('last_username' => $lastUsername, 'error' => $error));

    }//end loginAction()


}//end class
