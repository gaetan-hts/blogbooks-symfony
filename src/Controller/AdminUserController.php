<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/admin/user')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashPassword);
            $userRepository->save($user, true);
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function show(User $user, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, User $user, UserRepository $userRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Request $request, User $user, UserRepository $userRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
