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
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->getSession()->get('email') == null)
        {
            return $this->redirectToRoute('login');
        }
        if($request->isMethod("POST")){
            
            $title=$request->request->get("title");
            $description=$request->request->get("description");
            $price=$request->request->get("price");
            $coverImg=$request->request->get("coverImg");
            $location=$request->request->get("location");
            $openSpots=$request->request->get("openSpots");
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

            return $this->redirectToRoute('HomeController');

        }
        return $this->render('rent/index.html.twig', [
            'controller_name' => 'RentController',
        ]);
    }

    #[Route('/edit/update/{id}', name: 'update_post')]
    public function update(Request $request, EntityManagerInterface $entityManager, ApartRepository $apartRepository, $id): Response
    {
        if($request->getSession()->get('email') == null)
        {
            return $this->redirectToRoute('login');
        }

        $post = $apartRepository->find($id);

        if(!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }

        if($request->isMethod("POST")){
            $title = $request->request->get("title");
            $description = $request->request->get("description");
            $price = $request->request->get("price");
            $coverImg = $request->request->get("coverImg");
            $location = $request->request->get("location");
            $openSpots = $request->request->get("openSpots");

            $post->setTitle($title);
            $post->setDescription($description);
            $post->setPrice($price);
            $post->setCoverImg($coverImg);
            $post->setLocation($location);
            $post->setOpenSpots($openSpots);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_rent');
        }

        return $this->render('/manage_post/update.html.twig', [
            'controller_name' => 'RentController',
            'post' => $post,
        ]);
    }

}
