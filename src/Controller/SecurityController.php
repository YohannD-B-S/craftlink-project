<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class SecurityController extends AbstractController
{
    #[Route('/redirect', name: 'redirect_after_login')]
    public function redirectAfterLogin(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        if (in_array('ROLE_CLIENT', $user->getRoles())) {
            return $this->redirectToRoute('client-homeboard');
        } elseif (in_array('ROLE_ARTISAN', $user->getRoles())) {
            return $this->redirectToRoute('artisan-homeboard');
        } elseif (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('admin-homeboard');
        }

        return $this->redirectToRoute('default_home');
    }

}