<?php

namespace App\Controller\artisan;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class ArtisanHomeBoardController extends AbstractController
{
    #[Route('/artisan/homeboard', name: 'artisan-homeboard')]
    public function displayArtisanHomeBoard(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('artisan/homeboard.html.twig');
    }

}