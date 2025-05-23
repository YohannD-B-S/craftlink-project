<?php

namespace App\Controller\admin;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\ArticleImage;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminArticleController extends AbstractController{

    #[Route('/admin/create_article', name: 'admin-create_article')]
    public function CreateArticle(Request $request, EntityManagerInterface $entityManager ): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $content = $request->request->get('content');

            if (!$title || !$content) {
                return new Response('Titre et contenu sont requis.', Response::HTTP_BAD_REQUEST);
            }

            $article = new Article();
            $article->setTitle($title);
            $article->setContent($content);
            $article->setAuthor($this->getUser());
            $article->setCreatedAt(new \DateTime());            $article->setIsPublished(true);
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success','Votre article à bien été créé');

            return $this->redirectToRoute('admin-create_article');
        }

        return $this->render('admin/article/create.html.twig');
    }

    #[Route('/admin/list_articles', name: 'admin-list-articles')]
    public function ListArticles(EntityManagerInterface $entityManager): Response
    {
        $admin = $this->getUser(); // Récupérer l'utilisateur connecté

        // Vérifier si l'utilisateur est bien un admin
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            $this->addFlash('error', 'Accès refusé. Seuls les administrateurs peuvent voir cette page.');
            return $this->redirectToRoute('home'); // Redirige vers la page d'accueil
        }

        // Récupérer tous les articles en base
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('admin/article/list.html.twig', [
            'articles' => $articles
        ]);
    }
    #[Route('/admin/delete_article/{id}', name: 'admin-delete_article')]
    public function deleteArticle($id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager):Response{
        $article = $articleRepository->find($id);
        $entityManager->remove($article);
        $entityManager->flush();
        $this->addFlash('success','Votre article à bien été supprimé');
        return $this->redirectToRoute('admin-list-articles');
    }

    #[Route('/admin/update_article/{id}', name: 'admin-update_article')]
    public function updateArticle($id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $admin = $this->getUser();

        // Vérifier que l'utilisateur est bien un admin
        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('home');
        }

        $article = $articleRepository->find($id);

        // Vérifier si l'article existe
        if (!$article) {
            $this->addFlash('error', 'Article introuvable.');
            return $this->redirectToRoute('admin-list-articles');
        }

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $content = $request->request->get('content');
            $isPublished = $request->request->get('is_published') === 'on';

            try {
                // Correction de l'appel à la méthode `update()` et mise à jour de la date
                $article->update($title, $content, $isPublished);
                $article->setUpdatedAt(new \DateTime()); // Met à jour `updatedAt`

                $entityManager->persist($article);
                $entityManager->flush();
                $this->addFlash('success', 'Votre article a bien été modifié.');

                // 🔄 Redirection vers la liste des articles après modification
                return $this->redirectToRoute('admin-list-articles');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'Une erreur s\'est produite.');
            }
        }

        return $this->render('admin/article/update.html.twig', [
            'article' => $article
        ]);
    }
}