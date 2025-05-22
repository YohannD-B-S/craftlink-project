<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class AdminHomeBoardController extends AbstractController
{
    #[Route('/admin/homeboard', name: 'admin-homeboard')]
    public function displayAdminHomeBoard(){
        return $this->render('admin/homeboard.html.twig');
    }

}