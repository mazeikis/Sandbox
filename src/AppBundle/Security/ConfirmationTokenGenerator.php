<?php

namespace AppBundle\Security;

class ConfirmationTokenGenerator 
{
    public function generateConfirmationToken()
    {
        return rtrim(strtr(base64_encode($this->getRandomNumber()), '+/', '-_'), '=');
    }
    private function getRandomNumber()
    {
        return hash('sha256', uniqid(mt_rand(), true), true);
    }
}