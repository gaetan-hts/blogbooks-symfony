<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Article;
use App\Entity\User;
use App\Form\Comment1Type;
use DateTimeImmutable;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/article/{id}/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Article $article, CommentRepository $commentRepository): Response
    {

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException('Accès refusé. Veuillez vous connecter.');
        }

        $comment = new Comment();
        $comment->setCreatedAt(new DateTimeImmutable());
        $comment->setUser($this->getUser());
        $comment->setArticle($article);
        $form = $this->createForm(Comment1Type::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_article_show', [
                'id' => $article->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }
}
