<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/admin/category')]
class AdminCategoryController extends AbstractController
{
    #[Route('/', name: 'app_admin_category_index', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function index(CategoryRepository $categoryRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin_category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_category_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(Request $request, CategoryRepository $categoryRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_category_show', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function show(Category $category, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin_category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_category_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_category_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
