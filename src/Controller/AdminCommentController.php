<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTimeImmutable;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


#[Route('/admin/comment')]
class AdminCommentController extends AbstractController
{
    #[Route('/', name: 'app_admin_comment_index', methods: ['GET'])]
public function index(CommentRepository $commentRepository, AuthorizationCheckerInterface $authorizationChecker): Response
{
    if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
        return $this->redirectToRoute('app_login');
    }

    return $this->render('admin_comment/index.html.twig', [
        'comments' => $commentRepository->findAll(),
    ]);
}

    #[Route('/new', name: 'app_admin_comment_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(Request $request, CommentRepository $commentRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $comment = new Comment();
        $comment->setCreatedAt(new DateTimeImmutable());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_comment_show', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function show(Comment $comment, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin_comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_comment_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Comment $comment, CommentRepository $commentRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_comment_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
        }

        return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
