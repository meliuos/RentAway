<?php

namespace App\Controller;

use App\Entity\Apart;
use App\Repository\ApartRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ManagePostController extends AbstractController
{
    #[Route('/edit', name: 'app_manage_post')]
    public function index(SessionInterface $session,ApartRepository $apartRepository): Response
    {
        $session->start();
        if($session->get('email') == null )
        {
            return $this->redirectToRoute('login');
        }
        $email=$session->get('email');
        $aparts = $apartRepository->findBy(['mail' => $email]);
        return $this->render('manage_post/index.html.twig', [
            'aparts' => $aparts,
        ]);
    }
    #[Route('/edit/delete/{id<\d+>}', name: 'delete_post')]
    public function delete(ApartRepository $apartRepository,$id,ManagerRegistry $doctrine,SessionInterface $session): RedirectResponse
    {
        $session->start();
        $apart = $apartRepository->findByIdAndEmail($id,$session->get('email'));
        $entityManager = $doctrine->getManager();
        $entityManager->remove($apart);
        $entityManager->flush();
        return $this->redirectToRoute('app_manage_post');
    }
    #[Route('/edit/update/{id<\d+>}', name: 'update_post')]
    public function update(ApartRepository $apartRepository,$id,ManagerRegistry $doctrine): Response
    {
        $apart = $apartRepository->find($id);
        return $this->render('manage_post/update.html.twig', [
            'apart' => $apart,
        ]);
    }
}
