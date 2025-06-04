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
    #[Route('/artisan/mes-conversations', name: 'artisan_list_conversations')]
    public function listArtisanConversations(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos conversations.');
        }

        // Vérifier que l'utilisateur est un artisan
        $artisan = $entityManager->getRepository(Artisan::class)->findOneBy(['user' => $user]);
        if (!$artisan) {
            throw $this->createAccessDeniedException('Seuls les artisans ont accès à cette page.');
        }

        // Récupérer toutes les conversations où l’artisan est participant
        $conversations = $entityManager->getRepository(Conversation::class)->findBy([
                'participantOne' => $user
            ]) + $entityManager->getRepository(Conversation::class)->findBy([
                'participantTwo' => $user
            ]);

        return $this->render('artisan/conversations_list.html.twig', [
            'conversations' => $conversations
        ]);
    }
    #[Route('/mes-conversations', name: 'list_conversations')]
    public function listConversations(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos conversations.');
        }

        // Récupérer toutes les conversations de l’utilisateur (artisan ou client)
        $conversations = $entityManager->getRepository(Conversation::class)->findBy([
                'participantOne' => $user
            ]) + $entityManager->getRepository(Conversation::class)->findBy([
                'participantTwo' => $user
            ]);

        return $this->render('client/conversations_list.html.twig', [
            'conversations' => $conversations
        ]);
    }
    #[Route('/conversation/{id}', name: 'view_conversation')]
    public function viewConversation(int $id, Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder aux conversations.');
        }

        $conversation = $entityManager->getRepository(Conversation::class)->find($id);

        if (!$conversation || ($conversation->getParticipantOne() !== $user && $conversation->getParticipantTwo() !== $user)) {
            throw $this->createAccessDeniedException('Vous n’êtes pas autorisé à voir cette conversation.');
        }

        // ✅ Récupérer les messages de la conversation
        $messages = $entityManager->getRepository(Message::class)->findBy(
            ['conversation' => $conversation],
            ['sentAt' => 'ASC']
        );

        // ✅ Ajouter la création du formulaire pour répondre
        $form = $formFactory->createBuilder()
            ->add('content', TextareaType::class, ['label' => 'Votre message'])
            ->add('send', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = new Message();
            $message->setSender($user);
            $message->setRecipient($conversation->getParticipantOne() === $user ? $conversation->getParticipantTwo() : $conversation->getParticipantOne());
            $message->setConversation($conversation);
            $message->setContent($form->get('content')->getData());
            $message->setSentAt(new \DateTime());
            $message->setIsRead(false);

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Message envoyé !');
            return $this->redirectToRoute('view_conversation', ['id' => $conversation->getId()]);
        }

        $template = ($user === $conversation->getParticipantOne()) ? 'client/conversation.html.twig' : 'artisan/conversation.html.twig';

        return $this->render($template, [
            'conversation' => $conversation,
            'messages' => $messages,
            'form' => $form->createView()
        ]);
    }
}