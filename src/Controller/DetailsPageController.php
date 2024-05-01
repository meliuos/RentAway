<?php

namespace App\Controller;

use App\Repository\ApartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;


class DetailsPageController extends AbstractController
{
    #[Route('/details/{id}', name: 'app_details_page')]
    public function index($id,ApartRepository $apartRepository,Request $request): Response
    {
        $apart = $apartRepository->find($id);
        return $this->render('details_page/index.html.twig', [
            'apart' => $apart,
            'message' => $request->query->get('message')
        ]);
    }
    #[Route('/contact/{id}', name: 'contact')]
public function contacter($id, ApartRepository $apartRepository): Response
{
    $apart = $apartRepository->find($id);
    
    // Check if $apart is not null to avoid potential errors
    if (!$apart) {
        throw $this->createNotFoundException('Apart not found');
    }
    
    // Access the getMail() function on the $apart object
    $email = $apart->getMail();
    return $this->render('details_page/contact.html.twig', [
        'email' => $email,
        'id' => $id
    ]);
}
    #[Route('/send_email', name: 'send_email')]
    public function sendEmail(Request $request, MailerInterface $mailer, ApartRepository $apartRepository): Response
    {
        $id = $request->request->get('id');
        $apart = $apartRepository->find($id);
    
        if (!$apart) {
            throw $this->createNotFoundException('Apart not found');
        }
    
        if ($request->isMethod('POST')) {
            // Check if the "email" session variable is set
            $senderEmail = $request->getSession()->get('email');
            if (!$senderEmail) {
                // Redirect with an error message if the sender email is not set
                return $this->redirectToRoute('app_details_page', ['message' => 'Email not sent: Sender email not set', 'id' => $id]);
            }
    
            $email = (new Email())
                ->from("admin@demomailtrap.com")
                ->to("tazou1999blackshot@gmail.com")
                ->subject($request->request->get('object'))
                ->text($request->request->get('message'));
    
            try {
                $mailer->send($email);
                return $this->redirectToRoute('app_details_page', ['message' => 'Email sent successfully', 'id' => $id]);
            } catch (Exception $e) {
                return $this->redirectToRoute('app_details_page', ['message' => 'Email not sent', 'id' => $id]);
            }
        }
    
        // Handle GET requests or invalid POST requests
        return $this->redirectToRoute('app_details_page', ['id' => $id]);
    }

}
