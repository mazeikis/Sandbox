<?php

namespace AppBundle\Security;

class TokenGenerator
{


    public function generateToken()
    {
        return rtrim(strtr(base64_encode($this->getRandomNumber()), '+/', '-_'), '=');

    }


    private function getRandomNumber()
    {
        return hash('sha256', uniqid(mt_rand(), true), true);

    }


}
