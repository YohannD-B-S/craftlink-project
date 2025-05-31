<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ArtisanRepository;
class ArtisanController extends AbstractController
{
    #[Route('/search_artisan', name: 'search_artisan')]
    public function searchArtisan(Request $request, ArtisanRepository $artisanRepository, SessionInterface $session): Response
    {
        if (!$session->isStarted()) {
            $session->start();
        }

        $speciality = $request->query->get('speciality');
        $postalCode = $request->query->get('postal_code');

        if (!$speciality || !$postalCode) {
            return $this->redirectToRoute('home');
        }

        $artisans = $artisanRepository->findAvailableArtisans($speciality, $postalCode);
        $artisanCount = count($artisans);
        $searchExtended = false;

        if ($artisanCount === 0) {
            $searchExtended = true;
            $departmentCode = substr($postalCode, 0, 2);
            $artisans = $artisanRepository->findAvailableArtisans($speciality, $departmentCode . '%');
            $artisanCount = count($artisans);
        }

        return $this->render('search_results.html.twig', [
            'artisans' => $artisans,
            'speciality' => $speciality,
            'postalCode' => $postalCode,
            'searchExtended' => $searchExtended,
            'artisanCount' => $artisanCount
        ]);
    }
}