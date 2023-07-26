<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category_list')]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository('App\Entity\Category')->findAll();

        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/category/delete/{id}', name: 'category_delete')]
    public function deleteAction(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $categories = $em->getRepository('App\Entity\Category')->find($id);
        $em->remove($categories);
        $em->flush();

        $this->addFlash(
            'error',
            'Category deleted'
        );

        return $this->redirectToRoute('category_list');
    }

    #[Route('/category/create', name: 'category_create', methods: ['GET', 'POST'])]
    public function createAction(ManagerRegistry $doctrine, Request $request)
    {
        $categories = new Category();
        $form = $this->createForm(CategoryType::class, $categories);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($categories);
            $em->flush();

            $this->addFlash(
                'notice',
                'Category Added'
            );
            return $this->redirectToRoute('category_list');
        }
        return $this->renderForm('category/create.html.twig', ['form' => $form,]);
    }
}
