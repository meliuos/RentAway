<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sysmofony\Component\HttpFoundation\Request;
class ManagePostController extends AbstractController
{
    #[Route('/edit', name: 'app_manage_post')]
    public function index(Request $request): Response
    {
        if($request->getSession()->get('email') == null)
        {
            return $this->redirectToRoute('login');
        }
        return $this->render('manage_post/index.html.twig', [
            'controller_name' => 'ManagePostController',
        ]);
    }
}
