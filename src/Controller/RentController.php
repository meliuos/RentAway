<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Apart;
use App\Repository\ApartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class RentController extends AbstractController
{
    #[Route('/post', name: 'app_rent')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->getSession()->get('email') == null) {
            return $this->redirectToRoute('login');
        }

        if ($request->isMethod("POST")) {
            $title = $request->request->get("title");
            $description = $request->request->get("description");
            $price = $request->request->get("price");
            $coverImg = $request->request->get("coverImg");
            $location = $request->request->get("location");
            $openSpots = $request->request->get("openSpots");

            $apart = new Apart();
            $apart->setTitle($title);
            $apart->setDescription($description);
            $apart->setPrice($price);
            $apart->setCoverImg($coverImg);
            $apart->setLocation($location);
            $apart->setOpenSpots($openSpots);
            $apart->setReviewCount(0);
            $apart->setRating(5);
            $apart->setMail($request->getSession()->get('email'));

            $entityManager->persist($apart);
            $entityManager->flush();

            $this->addFlash('success', 'Post added successfully.');

            return $this->redirectToRoute('app_manage_post');
        }

        return $this->render('rent/index.html.twig', [
            'controller_name' => 'RentController',
        ]);
    }


}
