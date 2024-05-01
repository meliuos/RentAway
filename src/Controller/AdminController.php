<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(SessionInterface $session,UsersRepository $usersRepository): Response
    {
        if(($request->getSession()->get('email') == null) || ($request->getSession()->get('admin') != true))
        {
            return $this->redirectToRoute('login');
        }
        $users = $usersRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

}
