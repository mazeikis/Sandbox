<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManager;


use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class LoginAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $router;
    private $encoder;

    public function __construct(EntityManager $em, RouterInterface $router, UserPasswordEncoder $encoder)
    {
        $this->em      = $em;
        $this->router  = $router;
        $this->encoder = $encoder;
    }

    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() != '/login' || !$request->isMethod('POST')) {
            return;
        }
        return [
            'username' => $request->get('_username'),
            'password' => $request->get('_password')
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->em->getRepository('AppBundle:User')
            ->findOneBy(['username' => $credentials['username']]);
        if($user){
            return $user;
        }else{ 
            throw new CustomUserMessageAuthenticationException(
                'password'
            );
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $plainPassword = $credentials['password'];
        $isValid       = $this->encoder->isPasswordValid($user, $plainPassword);

        if(!$isValid){
            return false;
        }
        
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        $url = $this->router->generate('_home');
        return new RedirectResponse($url);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $url = $this->router->generate('_gallery');
        return new RedirectResponse($url);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $url = $this->router->generate('login');
        return new RedirectResponse($url);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
