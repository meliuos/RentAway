<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ApartRepository;


/*
     * @Route("/", name="HomeController")
*/

class HomeController extends AbstractController{
    public function index(SessionInterface $session,ApartRepository $apartRepository): Response
    {
        $session->start();
        $session->get('authenticated');
        $aparts = $apartRepository->findAll();

        return $this->render('index.html.twig',[
            'session' => $session,
            'aparts' => $aparts
        ]);
    }
}