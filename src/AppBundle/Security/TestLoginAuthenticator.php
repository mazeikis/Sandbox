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
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class TestLoginAuthenticator extends AbstractGuardAuthenticator
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
        return [
            'username' => $request->getUser(),
            'password' => $request->getPassword()
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
                'Password does not match the user '.$credentials['username']
            );
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
        return null;
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
