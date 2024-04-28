<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController {
    public function index(Request $request): Response
    {
        // Check if user is authenticated
        if (!$request->getSession()->has('email')) {
            return new RedirectResponse('/login');
        }
        return $this->render('admin/index.html.twig');
    }   
}