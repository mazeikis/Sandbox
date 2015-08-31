<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{


    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error               = $authenticationUtils->getLastAuthenticationError();
        if ($error !== null){
        	$flash = $this->get('braincrafted_bootstrap.flash');
        	$flash->error('Wrong password. Please try again or use Reset Password option!');
        }

        return $this->redirectToRoute('_home');

    }


}
