<?php

namespace AppBundle\Security;

class ConfirmationTokenGenerator
{


    public function generateConfirmationToken()
    {
        return rtrim(strtr(base64_encode($this->getRandomNumber()), '+/', '-_'), '=');

    }//end generateConfirmationToken()


    private function getRandomNumber()
    {
        return hash('sha256', uniqid(), true);

    }//end getRandomNumber()


}//end class
