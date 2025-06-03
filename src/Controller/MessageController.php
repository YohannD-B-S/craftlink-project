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
use App\Entity\Conversation;
use App\Entity\Artisan;
use App\Entity\User;

class MessageController extends AbstractController
{
    #[Route('/contact_artisan/{id}', name: 'contact_artisan', methods: ['GET', 'POST'])]
    public function contactArtisan(
        Request $request,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        Artisan $artisan
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \LogicException('L’utilisateur connecté n’est pas du type attendu.');
        }
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour envoyer un message.');
            return $this->redirectToRoute('login');
        }

        // Vérifier si une conversation existe déjà
        $conversation = $entityManager->getRepository(Conversation::class)->findOneBy([
            'participantOne' => $user,
            'participantTwo' => $artisan->getUser()
        ]) ?? $entityManager->getRepository(Conversation::class)->findOneBy([
            'participantOne' => $artisan->getUser(),
            'participantTwo' => $user
        ]);

        // Si aucune conversation n'existe, en créer une
        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setParticipantOne($user);
            $conversation->setParticipantTwo($artisan->getUser());

            $entityManager->persist($conversation);
            $entityManager->flush();
        }

        // Création du formulaire d'envoi de message
        $form = $formFactory->createBuilder()
            ->add('content', TextareaType::class, ['label' => 'Votre message'])
            ->add('send', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement du message
            $message = new Message();
            $message->setSender($user);
            $message->setRecipient($artisan->getUser());
            $message->setConversation($conversation);
            $message->setContent($form->get('content')->getData());
            $message->setSentAt(new \DateTime());

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Votre message a été envoyé.');
            return $this->redirectToRoute('view_conversation', ['id' => $conversation->getId()]);
        }

        return $this->render('client/contact_artisan.html.twig', [
            'form' => $form->createView(),
            'artisan' => $artisan
        ]);
    }

    #[Route('/conversation/{id}', name: 'view_conversation', methods: ['GET', 'POST'])]
    public function viewConversation(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $artisan = $this->getUser()->getArtisan();

        if (!$artisan) {
            $this->addFlash('danger', 'Vous devez être un artisan pour accéder à cette page.');
            return $this->redirectToRoute('home');
        }

        // Récupérer la conversation et ses messages
        $conversation = $entityManager->getRepository(Conversation::class)->find($id);
        $messages = $entityManager->getRepository(Message::class)->findBy(
            ['conversation' => $conversation],
            ['sentAt' => 'ASC']
        );

        // Formulaire de réponse
        $formFactory = $this->container->get('form.factory');
        $form = $formFactory->createBuilder()
            ->add('content', TextareaType::class, ['label' => 'Votre réponse'])
            ->add('send', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $responseMessage = new Message();
            $responseMessage->setSender($artisan->getUser());
            $responseMessage->setRecipient($conversation->getParticipantTwo());
            $responseMessage->setConversation($conversation);
            $responseMessage->setContent($form->get('content')->getData());
            $responseMessage->setSentAt(new \DateTime());

            $entityManager->persist($responseMessage);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réponse a été envoyée.');
            return $this->redirectToRoute('view_conversation', ['id' => $id]);
        }

        return $this->render('artisan/message/conversation.html.twig', [
            'messages' => $messages,
            'form' => $form->createView(),
            'conversation' => $conversation
        ]);
    }
}