<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class RentController extends AbstractController
{
    #[Route('/post', name: 'app_rent')]
    public function index(Request $request
    ): Response
    {
        if($request->getSession()->get('email') == null)
        {
            return $this->redirectToRoute('login');
        }
        return $this->render('rent/index.html.twig', [
            'controller_name' => 'RentController',
        ]);
    }
}
