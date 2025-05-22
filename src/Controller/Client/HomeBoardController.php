<?php

namespace App\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class HomeBoardController extends AbstractController
{
    #[Route('/client/homeboard', name: 'client-homeboard')]
    public function displayHomeBoard(){
        return $this->render('client/homeboard.html.twig');
    }
}