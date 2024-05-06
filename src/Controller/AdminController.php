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


    #[Route('/admin/delete/{id}', name: 'delete_user')]

    public function delete(Request $request, EntityManagerInterface $entityManager, UsersRepository $userRepository, $id): Response
    {
        if(($request->getSession()->get('email') == null) || ($request->getSession()->get('admin') != true))
        {
            return $this->redirectToRoute('login');
        }
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        if ($user->isAdmin()) {
            $this->addFlash('erroradmin', 'You cannot delete an admin user.');
            return $this->redirectToRoute('admin');
        }

        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', 'User deleted successfully.');
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
    public function edit(Request $request, EntityManagerInterface $entityManager, UsersRepository $userRepository, $id): Response
    {
        if(($request->getSession()->get('email') == null) || ($request->getSession()->get('admin') != true))
        {
            return $this->redirectToRoute('login');
        }
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $existingUser = $userRepository->findOneBy(['mail' => $email]);
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                $this->addFlash('error', 'Email is already in use.');
                return $this->redirectToRoute('edit_user', ['id' => $id]);
            }

            $user->setMail($email);
            if (!empty($password)) {

                $user->setPassword($password);
            }

            $entityManager->flush();
            $this->addFlash('success', 'User updated successfully.');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/edit.html.twig', [
            'user' => $user,
        ]);
    }

}
