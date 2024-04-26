<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(Request $request): Response
    {
        // Check if the form is submitted
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $admin = $request->request->get('role');
            
            // Check if username and password are valid (example)
            if ($username === 'admin' && $password === 'password') {
                $request->getSession()->set('username',$username);
                $request->getSession()->set('role',$admin);
                // Redirect to homepage
                return $this->redirectToRoute('');
            } else {
                // Invalid credentials
                $this->addFlash('error', 'Invalid username or password.');
            }
        }
        return $this->render('login.html.twig');
    }
    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        // Clear session variables
        $request->getSession()->clear();
        return $this->redirectToRoute('');
    }
}
