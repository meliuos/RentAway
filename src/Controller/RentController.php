<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Apart;
use App\Repository\ApartRepository;

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
        if($request->isMethod("GET")){
            
            $title=$request->request->get("title");
            $description=$request->request->get("description");
            $price=$request->request->get("price");
            $coverImg=$request->request->get("coverImg");
            $location=$request->request->get("location");
            $openSpots=$request->request->get("openSpots");
            $apart = new Apart();
            

        }
        return $this->render('rent/index.html.twig', [
            'controller_name' => 'RentController',
        ]);
    }
}
