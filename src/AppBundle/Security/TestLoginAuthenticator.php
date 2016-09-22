<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;

class TestLoginAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $encoder;

    public function __construct(EntityManager $em, UserPasswordEncoder $encoder)
    {
        $this->em      = $em;
        $this->encoder = $encoder;
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->getUser(),
            'password' => $request->getPassword()
        ];
        if ($credentials['username'] === null) {
            return null;
        }
        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $userProvider->loadUserByUsername($credentials['username']);
        if($user instanceof User){
            return $user;
        }else{ 
            return null;
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $plainPassword = $credentials['password'];
        $isValid       = $this->encoder->isPasswordValid($user, $plainPassword);

        return $isValid;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response(null, 403);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response(null, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
