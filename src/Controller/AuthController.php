<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(Request $request, UsersRepository $usersRepository): Response
    {
        // Check if the form is submitted
        if ($request->isMethod('POST')) {
            $mail = $request->request->get('mail');
            $password = $request->request->get('password');
            
            // Fetch user from database by email
            $user = $usersRepository->findOneBy(['mail' => $mail]);
            
            // Check if user exists and password matches
            if ($user && $password == $user->getPassword()) {
                // Set user session
                $this->setUserSession($request, $user);
                
                // Redirect to homepage
                return $this->redirectToRoute('HomeController');
            } else {
                // Invalid credentials
                $this->addFlash('error', 'Invalid email or password.');
            }
        }
        $this->addFlash('error', 'Post');
        return $this->render('login.html.twig');
    }
    
    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        // Clear session variables
        $request->getSession()->clear();
        return $this->redirectToRoute('HomeController');
    }
    
    #[Route('/signup', name: 'signup')]
    public function signup(Request $request, UsersRepository $usersRepository): Response
    {
        // Check if the form is submitted
        if ($request->isMethod('POST')) {
            $mail = $request->request->get('mail');
            $password = $request->request->get('password');
            
            // Check if user with the same email already exists
            $existingUser = $usersRepository->findOneBy(['mail' => $mail]);
            if ($existingUser) {
                $this->addFlash('error', 'User with this email already exists.');
                return $this->redirectToRoute('signup');
            }
            
            // Create new user entity
            $user = new Users();
            $user->setMail($mail);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setAdmin(false); // Set admin flag as needed
            
            // Save user to database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            
            // Set user session
            $this->setUserSession($request, $user);
            
            // Redirect to homepage
            return $this->redirectToRoute('HomeController');
        }
        
        return $this->render('signup.html.twig');
    }
    
    // Helper method to set user session
    private function setUserSession(Request $request, Users $user): void
    {
        $request->getSession()->set('username', $user->getMail());
        $request->getSession()->set('admin', $user->isAdmin());
    }
}
