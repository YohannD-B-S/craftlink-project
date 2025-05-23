<?php

namespace App\Controller\artisan;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\ArticleImage;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArtisanArticleController extends AbstractController{

    #[Route('/artisan/create_article', name: 'artisan-create_article')]
    public function CreateArticle(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        // Vérifier que l'utilisateur est bien un artisan
        if (!$user || !in_array('ROLE_ARTISAN', $user->getRoles())) {
            $this->addFlash('error', 'Accès refusé. Seuls les artisans peuvent créer des articles.');
            return $this->redirectToRoute('home');
        }

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $content = $request->request->get('content');

            if (!$title || !$content) {
                return new Response('Titre et contenu sont requis.', Response::HTTP_BAD_REQUEST);
            }

            $article = new Article();
            $article->setTitle($title);
            $article->setContent($content);
            $article->setAuthor($user);
            $article->setCreatedAt(new \DateTime()); // Corrige `setCreatedDate()` en `setCreatedAt()`
            $article->setIsPublished(true);

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Votre article a bien été créé');

            return $this->redirectToRoute('artisan-list-articles'); // Redirige vers la liste des articles artisans
        }

        return $this->render('artisan/article/create.html.twig'); // Corrige le template pour correspondre aux artisans
    }

    #[Route('/artisan/list_articles', name: 'artisan-list-articles')]
    public function ListArticles(EntityManagerInterface $entityManager): Response
    {
        $artisan = $this->getUser(); // Récupérer l'utilisateur connecté

        // Vérifier que l'utilisateur est bien un artisan
        if (!in_array('ROLE_ARTISAN', $artisan->getRoles())) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('home');
        }

        // Récupérer uniquement les articles de cet artisan
        $articles = $entityManager->getRepository(Article::class)->findBy([
            'author' => $artisan
        ]);

        return $this->render('artisan/article/list.html.twig', [
            'articles' => $articles
        ]);
    }
    #[Route('/artisan/update_article/{id}', name: 'artisan-update_article')]
    public function updateArticle($id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $artisan = $this->getUser(); // Récupérer l'utilisateur connecté

        // Vérifier que l'utilisateur est bien un artisan
        if (!$artisan || !in_array('ROLE_ARTISAN', $artisan->getRoles())) {
            $this->addFlash('error', 'Accès refusé. Seuls les artisans peuvent modifier leurs articles.');
            return $this->redirectToRoute('home'); // Redirige vers la page d'accueil
        }

        $article = $articleRepository->find($id);

        // Vérifier si l'article existe
        if (!$article) {
            $this->addFlash('error', 'Article introuvable.');
            return $this->redirectToRoute('artisan-list-articles');
        }

        // Vérifier si l’utilisateur connecté est bien l’auteur de l’article
        if ($article->getAuthor() !== $artisan) {
            $this->addFlash('error', 'Vous ne pouvez modifier que vos propres articles.');
            return $this->redirectToRoute('artisan-list-articles');
        }

        // Traitement de la mise à jour
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $content = $request->request->get('content');
            $isPublished = $request->request->get('is_published') === 'on';

            try {
                $article->setTitle($title);
                $article->setContent($content);
                $article->setIsPublished($isPublished);
                $article->setUpdatedAt(new \DateTime()); // Met à jour la date de modification

                $entityManager->persist($article);
                $entityManager->flush();
                $this->addFlash('success', 'Votre article a bien été modifié.');

                // Rediriger vers la liste après mise à jour
                return $this->redirectToRoute('artisan-list-articles');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'Une erreur s\'est produite.');
            }
        }

        return $this->render('artisan/article/update.html.twig', [
            'article' => $article
        ]);
    }
}