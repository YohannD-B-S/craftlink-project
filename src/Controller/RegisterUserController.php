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
            // Récupération des données du formulaire avec valeurs par défaut
            $lastName = $request->request->get('lastName') ?? '';
            $firstName = $request->request->get('firstName') ?? '';
            $email = $request->request->get('email') ?? '';
            $password = $request->request->get('password') ?? '';
            $address = $request->request->get('address') ?? '';
            $postalCode = $request->request->get('postal_code') ?? '';
            $phoneNumber = $request->request->get('phone_number') ?? '';

            // Vérifications basiques
            if (empty($lastName) || empty($firstName) || empty($email) || empty($password)) {
                $this->addFlash('danger', 'Tous les champs requis doivent être remplis.');
                return $this->redirectToRoute('register_client');
            }

            // Vérification du format du numéro de téléphone (optionnel)
            if (!preg_match('/^\+?\d{10,15}$/', $phoneNumber)) {
                $this->addFlash('danger', 'Le format du numéro de téléphone est invalide.');
                return $this->redirectToRoute('register_client');
            }

            // Création de l'utilisateur
            $user = new User();
            $passwordHash = $userPasswordHasher->hashPassword($user, $password);
            $created_at = new \DateTime();

            // Ajout des informations du client
            $user->createClient($email, $passwordHash, $firstName, $lastName, $address, $postalCode, $phoneNumber, $created_at);

            try {
                // Persistance en base
                $entityManager->persist($user);
                $entityManager->flush();

                // Correction du message flash
                $this->addFlash('success', 'Votre compte client a bien été créé');

                // Redirection vers la page client
                return $this->redirectToRoute('client-homeboard');
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Une erreur s\'est produite : ' . $exception->getMessage());
            }
        }

        return $this->render('register_client.html.twig');
    }

    #[Route('/register_artisan', name: 'register_artisan')]
    public function displayCreateArtisan(Request $request, UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($request->isMethod('POST')){

            $firstName=$request->request->get('firstName');
            $lastName=$request->request->get('lastName');
            $email=$request->request->get('email');
            $adress=$request->request->get('address');
            $postal_code=$request->request->get('postal_code');
            $phone=$request->request->get('phone_number');
            $speciality=$request->request->get('speciality');
            $password=$request->request->get(key:'password');
            $user=new User();
            $passwordHash=$userPasswordHasher->hashPassword($user,$password);
            $created_at=new \DateTime();
            $user->CreateArtisan($email, $passwordHash, $firstName, $speciality, $phone, $postal_code, $adress, $lastName, $created_at, $entityManager);
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