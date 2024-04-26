<?php

namespace App\Controller;

use App\Repository\ApartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailsPageController extends AbstractController
{
    #[Route('/details/{id}', name: 'app_details_page')]
    public function index($id,ApartRepository $apartRepository): Response
    {
        $apart = $apartRepository->find($id);
        return $this->render('details_page/index.html.twig', [
            'apart' => $apart
        ]);
    }
}
