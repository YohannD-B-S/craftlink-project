<?php

namespace App\Controller\admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    #[Route('/admin/user', name: 'admin-user')]
    public function listUsers(UserRepository $userRepository, Request $request): Response
    {
        $admin = $this->getUser();

        // Vérifier si l'utilisateur est bien un admin
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('home');
        }

        // Récupérer le paramètre de tri
        $sortBy = $request->query->get('sort', 'name'); // Valeur par défaut : tri par nom
        $order = $request->query->get('order', 'asc'); // Valeur par défaut : ascendant

        // Construire la requête
        $queryBuilder = $userRepository->createQueryBuilder('u');

        if ($sortBy === 'name') {
            $queryBuilder->orderBy('u.lastName', $order)->addOrderBy('u.firstName', $order);
        } elseif ($sortBy === 'createdAt') {
            $queryBuilder->orderBy('u.createdAt', $order);
        }

        $users = $queryBuilder->getQuery()->getResult();

        return $this->render('admin/user/list.html.twig', [
            'users' => $users,
            'sort' => $sortBy,
            'order' => $order
        ]);
    }
}