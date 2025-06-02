<?php


namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use App\Entity\Message;
use App\Entity\Artisan;
use App\Entity\User;

class MessageController extends AbstractController
{
    #[Route('/contact_artisan/{id}', name: 'contact_artisan', methods: ['GET', 'POST'])]
    public function contactArtisan(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, Artisan $artisan): Response
    {
        // Création du formulaire d'envoi de message
        $form = $formFactory->createBuilder()
            ->add('content', TextareaType::class, ['label' => 'Votre message'])
            ->add('send', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            // Vérification de connexion
            if (!$user) {
                $this->addFlash('danger', 'Vous devez être connecté pour envoyer un message.');
                return $this->redirectToRoute('login');
            }

            // Enregistrement du message en base de données
            $message = new Message();
            $message->setSender($user);
            $message->setRecipient($artisan);
            $message->setContent($form->get('content')->getData());
            $message->setSentAt(new \DateTime());

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Votre message a été envoyé.');
            return $this->redirectToRoute('view_messages');
        }

        return $this->render('client/contact_artisan.html.twig', [
            'form' => $form->createView(),
            'artisan' => $artisan
        ]);
    }

    #[Route('/messages', name: 'view_messages', methods: ['GET'])]
    public function viewMessages(EntityManagerInterface $entityManager): Response
    {
        $artisan = $this->getUser();
        $messages = $entityManager->getRepository(Message::class)->findBy(['recipient' => $artisan]);

        return $this->render('artisan/message/messages.html.twig', ['messages' => $messages]);
    }
}