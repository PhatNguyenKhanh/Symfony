<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\User;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class ProductController extends AbstractController
{
    #[Route('/product', name: 'product_list')]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository('App\Entity\Product')->findAll();

        return $this->render('product/index.html.twig', ['products' => $products]);
    }

    #[Route('/product/details/{id}', name: 'product_details')]
    public function detailsAction(ManagerRegistry $doctrine, $id)
    {
        $products = $doctrine->getRepository('App\Entity\Product')->find($id);

        return $this->render('product/details.html.twig', ['products' => $products]);
    }

    #[Route('/product/create', name: 'product_create', methods: ['GET', 'POST'])]
    public function createAction(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger)
    {
        $products = new Product();
        $form = $this->createForm(ProductType::class, $products);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // upload file
            $Image = $form->get('productImage')->getData();
            if ($Image) {
                $originalFilename = pathinfo($Image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $Image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $Image->move(
                        $this->getParameter('productImages_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $products->setImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );// ... handle exception if something happens during file upload
            }

            $em = $doctrine->getManager();
            $em->persist($products);
            $em->flush();

            $this->addFlash(
                'notice',
                'Product Added'
            );
            return $this->redirectToRoute('product_list');
        }
        return $this->renderForm('product/create.html.twig', ['form' => $form,]);
    }

    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function deleteAction(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $products = $em->getRepository('App\Entity\Product')->find($id);
        $em->remove($products);
        $em->flush();

        $this->addFlash(
            'error',
            'Product deleted'
        );

        return $this->redirectToRoute('product_list');
    }

    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function editAction(ManagerRegistry $doctrine, int $id, Request $request, SluggerInterface $slugger): Response
    {
        $entityManager = $doctrine->getManager();
        $products = $entityManager->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class, $products);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // upload file
            $Image = $form->get('productImage')->getData();
            if ($Image) {
                $originalFilename = pathinfo($Image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $Image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $Image->move(
                        $this->getParameter('productImages_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $products->setImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );// ... handle exception if something happens during file upload
            }

            $em = $doctrine->getManager();
            $em->persist($products);
            $em->flush();
            return $this->redirectToRoute('product_list', [
                'id' => $products->getId()
            ]);

        }
        return $this->renderForm('product/edit.html.twig', ['form' => $form,]);
    }

    public function saveChanges(ManagerRegistry $doctrine, $form, $request, $products)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $products->setName($request->request->get('product')['name']);
            $products->setCategory($request->request->get('product')['category']);
            $products->setPrice($request->request->get('product')['price']);
            $products->setPrice($request->request->get('product')['quantity']);
            $products->setDescription($request->request->get('product')['description']);
            $products->setDate($request->request->get('product')['date']);
            $em = $doctrine->getManager();
            $em->persist($products);
            $em->flush();

            return true;
        }

        return false;
    }
}
