<?php

namespace App\Controller;

use App\Repository\ArtisanRepository;
use App\Entity\Artisan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArtisanController extends AbstractController{

    #[Route('/search_artisan', name: 'search_artisan')]
    public function searchArtisan(Request $request, ArtisanRepository $artisanRepository): Response
    {
        $speciality = $request->query->get('speciality');
        $postalCode = $request->query->get('postal_code');

        // Recherche des artisans avec la méthode du repository
        $artisans = $artisanRepository->findAvailableArtisans($speciality, $postalCode);

        return $this->render('search_results.html.twig', [
            'artisans' => $artisans,
            'speciality' => $speciality,
            'postalCode' => $postalCode
        ]);
    }

}