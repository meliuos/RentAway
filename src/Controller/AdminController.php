<?php

namespace App\Controller;
use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Repository\ApartRepository;
use App\Entity\Apart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


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
    public function addAdmin(UsersRepository $usersRepository,SessionInterface $session,Request $request,EntityManagerInterface $entityManager): Response
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

            //*** Check if user with the same email already exists
            $existingUser = $usersRepository->findOneBy(['mail' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'User with this email already exists.');
                return $this->redirectToRoute('signup');
            }

            $user = new Users();
            $user->setMail($email);
            $user->setPassword($password);
            $user->setAdmin(true);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('addAdmin');
        }
        return $this->render('admin/registerAdmin.html.twig');
    }
    #[Route('/admin/managePost',name:'managePost')]
    public function managePost(ApartRepository $aparts,SessionInterface $session,Request $request): Response{
        if(($request->getSession()->get('email') == null) || ($request->getSession()->get('admin') != true))
        {
            return $this->redirectToRoute('login');
        }
        //DELETE POST IF THE FORM IS SUBMITTED
        $aparts = $aparts->findAll();
        return $this->render('admin/posts.html.twig', [
            'aparts' => $aparts,
        ]);
    }

    #[Route('/admin/edit/{id}', name: 'edit_user')]
public function edit(Request $request, $id): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $user = $entityManager->getRepository(User::class)->find($id);

    if (!$user) {
        throw $this->createNotFoundException('User not found');
    }

    if ($request->isMethod('POST')) {
        $user->setUsername($request->request->get('username'));
        $user->setEmail($request->request->get('email'));

        // Add more fields as needed

        $entityManager->flush();

        return $this->redirectToRoute('admin');
    }

    return $this->render('admin/edit.html.twig', [
        'user' => $user,
    ]);
}




}
