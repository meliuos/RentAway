<?php

namespace App\Controller;

use App\Repository\ApartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;


class DetailsPageController extends AbstractController
{
    #[Route('/details/{id<\d+>}', name: 'app_details_page')]
    public function index($id,ApartRepository $apartRepository,Request $request): Response
    {
        $apart = $apartRepository->find($id);
        return $this->render('details_page/index.html.twig', [
            'apart' => $apart,
            'message' => $request->query->get('message')
        ]);
    }
    #[Route('/contact/{id<\d+>}', name: 'contact')]
public function contacter($id, ApartRepository $apartRepository): Response
{
    $apart = $apartRepository->find($id);

    // Check if $apart is not null to avoid potential errors
    if (!$apart) {
        throw $this->createNotFoundException('Apart not found');
    }

    // Access the getMail() function on the $apart object
    $email = $apart->getMail();
    $coverImg =$apart->getCoverImg();
    $titlehouse = $apart->getTitle();
    return $this->render('details_page/contact.html.twig', [
        'email' => $email,
        'id' => $id,
        'img'=> $coverImg,
        'title'=>$titlehouse
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
            $senderEmail =$request->request->get('email');

            if (!$senderEmail) {
                return $this->redirectToRoute('app_details_page', ['message' => 'Email not sent: Sender email not set', 'id' => $id]);
            }
            $email_address = $apart->getMail();

            $email = (new Email())
                ->from($senderEmail)
                ->to($email_address)
                ->subject($request->request->get('object'))
                ->text($request->request->get('message'));

            try {
                $mailer->send($email);
                return $this->redirectToRoute('app_details_page', ['message' => 'Email sent successfully', 'id' => $id]);
            } catch (TransportExceptionInterface $e) {
                    $errorMessage = 'Email not sent: ' . $e->getMessage();
                    return $this->redirectToRoute('app_details_page', ['message' => $errorMessage, 'id' => $id]);
                }

        }

        return $this->redirectToRoute('app_details_page', ['id' => $id]);
    }
}
