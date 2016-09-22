<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Braincrafted\Bundle\BootstrapBundle\Session\FlashMessage;
use Doctrine\ORM\EntityManager;


use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class WebLoginAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $router;
    private $encoder;
    private $flash;

    public function __construct(EntityManager $em, RouterInterface $router, UserPasswordEncoder $encoder, FlashMessage $flash)
    {
        $this->em      = $em;
        $this->router  = $router;
        $this->encoder = $encoder;
        $this->flash   = $flash;
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
            ->findOneBy(array('username' => $credentials['username']));
        if($user){
            return $user;
        }else{ 
            throw new CustomUserMessageAuthenticationException(
                'Password does not match.'
            );
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $plainPassword = $credentials['password'];
        $isValid       = $this->encoder->isPasswordValid($user, $plainPassword);

        if($isValid){
            return true;
        }

        return false;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        $this->flash->error('No mathcing username/password combination found. Please try again or use password reset.');
        $url = $this->router->generate('_home');
        return new RedirectResponse($url);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
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
