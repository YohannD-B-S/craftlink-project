<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class RegisterUserController extends AbstractController{

    #[Route('/register_client', name: 'register_client')]
    public function displayCreateClient(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        if ($request->isMethod('POST')) {
            $lastName = $request->request->get('lastName');
            $firstName = $request->request->get('firstName');
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $user = new User();
            $passwordHash = $userPasswordHasher->hashPassword($user, $password);
            $created_at = new \DateTime();
            $user->CreateClient($email, $passwordHash, $firstName, $lastName, $created_at);

            try {
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Votre compte artisan a bien été créé');

                // Redirection vers la page de connexion
                return $this->redirectToRoute('client-homeboard');
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Une erreur s\'est produite');
            }
        }
        return $this->render('register_client.html.twig');
    }

    #[Route('/register_artisan', name: 'register_artisan')]
    public function displayCreateArtisan(Request $request, UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($request->isMethod('POST')){

            $lastName=$request->request->get('lastName');
            $firstName=$request->request->get('firstName');
            $email=$request->request->get('email');
            $password=$request->request->get(key:'password');
            $user=new User();
            $passwordHash=$userPasswordHasher->hashPassword($user,$password);
            $created_at=new \DateTime();
            $user->CreateArtisan($email,$passwordHash,$firstName,$lastName, $created_at);


            try{
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success','Votre compte artisan à bien été créé');
                return $this->redirectToRoute('artisan-homeboard');
            }catch(\Exception $exception){
                $this->addFlash('danger','Une erreur s\'est produite' );
            }
        }
        return $this->render('register_artisan.html.twig');
    }


}