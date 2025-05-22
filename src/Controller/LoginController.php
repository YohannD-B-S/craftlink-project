<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function displayLogin(AuthenticationUtils $authentication): Response
    {
        $error= $authentication->getLastAuthenticationError();
        return $this->render('login.html.twig',[
            'error' => $error
    ]);

    }

    #[Route('/redirect', name: 'redirect_after_login')]
    public function redirectAfterLogin(): Response
    {
        $user = $this->getUser();

        if ($user->getRoles() == 'ROLE_CLIENT') {
            return $this->redirectToRoute('client-homeboard');
        } elseif ($user->getRoles() == 'ROLE_ARTISAN') {
            return $this->redirectToRoute('artisan-homeboard');
        } elseif ($user->getRoles() == 'ROLE_ADMIN') {
            return $this->redirectToRoute('admin-homeboard');
        }
        // ... (other roles)

        return $this->redirectToRoute('default_home');
    }



#[Route('/logout', name: 'logout', methods: ['GET', 'POST'])]
    public function displayLogout()
    {


    }

}