<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository('App\Entity\Product')->findAll();

        return $this->render('user/index.html.twig', ['products' => $products]);
    }

    #[Route('/user/details/{id}', name: 'user_details')]
    public function detailsAction(ManagerRegistry $doctrine, $id)
    {
        $products = $doctrine->getRepository('App\Entity\Product')->find($id);

        return $this->render('user/details.html.twig', ['products' => $products]);
    }
}
