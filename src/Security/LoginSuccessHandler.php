<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        if (in_array('ROLE_CLIENT', $user->getRoles())) {
        return new RedirectResponse($this->router->generate('client-homeboard'));
    }

        if (in_array('ROLE_ARTISAN', $user->getRoles())) {
        return new RedirectResponse($this->router->generate('artisan-homeboard'));
    }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
        return new RedirectResponse($this->router->generate('admin-homeboard'));
    }

        return new RedirectResponse($this->router->generate('default_home'));
    }
}
