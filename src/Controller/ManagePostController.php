<?php

namespace App\Controller;

use App\Entity\Apart;
use App\Repository\ApartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ManagePostController extends AbstractController
{
    #[Route('/edit', name: 'app_manage_post')]
    public function index(SessionInterface $session,ApartRepository $apartRepository): Response
    {
        $session->start();
        if($session->get('email') == null )
        {
            return $this->redirectToRoute('login');
        }
        $email=$session->get('email');
        $aparts = $apartRepository->findBy(['mail' => $email]);
        return $this->render('manage_post/index.html.twig', [
            'aparts' => $aparts,
        ]);
    }
}
