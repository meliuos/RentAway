<?php

namespace App\Controller;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(SessionInterface $session,UsersRepository $usersRepository,Request $request): Response
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
    #[Route('/admin/delete/{id<\d+>}', name: 'delete_user')]
    public function delete(UsersRepository $usersRepository,$id,SessionInterface $session): Response
    {
        $session->start();
        if($session->get('admin') != true)
        {
            return $this->redirectToRoute('login');
        }
        $user = $usersRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('admin');
    }
    #[Route('/admin/addAdmin',name:'addAdmin')]
    public function addAdmin(UsersRepository $usersRepository,SessionInterface $session,Request $request): Response
    {
        $session->start();
        if($session->get('admin') != true)
        {
            return $this->redirectToRoute('login');
        }
        if($request->isMethod('POST'))
        {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $user = new Users();
        }
    }
}
