<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends AbstractController
{
    public function login(Request $request): Response
    {
        // Check if the form is submitted
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $admin = $request->request->get('isAdmin');
            
            // Check if username and password are valid (example)
            if ($username === 'admin' && $password === 'password') {
                $request->getSession()->set('authenticated', true);
                $request->getSession()->set('user', ['username' => $username, 'role' => 'admin']);

                return $this->redirectToRoute('');
            } else {
                // Invalid credentials
                $this->addFlash('error', 'Invalid username or password.');
            }
        }

        return $this->render('login.html.twig');
    }

    public function logout(Request $request): Response
    {
        // Clear session variables
        $request->getSession()->clear();

        return $this->redirectToRoute('');
    }
}
