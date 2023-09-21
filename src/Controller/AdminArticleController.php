<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/admin/article')]
class AdminArticleController extends AbstractController
{
    #[Route('/', name: 'app_admin_article_index', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function index(ArticleRepository $articleRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin_article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_article_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(Request $request, ArticleRepository $articleRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_article_show', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function show(Article $article, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin_article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_article_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_article_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
