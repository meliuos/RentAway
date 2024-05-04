<?php

namespace App\Controller;

use App\Entity\Apart;
use App\Repository\ApartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function delete(ApartRepository $apartRepository, $id, ManagerRegistry $doctrine, SessionInterface $session): RedirectResponse
    {
        $session->start();
        $email = $session->get('email');
        $apart = $apartRepository->findByIdAndEmail($id, $email);

        if (!$apart) {
            throw $this->createNotFoundException('The post does not exist for the current user');
        }

        $entityManager = $doctrine->getManager();

        // Check if the entity is managed by Doctrine
        if (!$entityManager->contains($apart)) {
            throw new \Exception('The entity is not managed by Doctrine');
        }

        $entityManager->remove($apart);
        $entityManager->flush();

        return $this->redirectToRoute('app_manage_post');
    }

    #[Route('/edit/update/{id}', name: 'update_post')]
    public function update(Request $request, EntityManagerInterface $entityManager, ApartRepository $apartRepository, $id): Response
    {
        if($request->getSession()->get('email') == null)
        {
            return $this->redirectToRoute('login');
        }

        $post = $apartRepository->find($id);

        if(!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }

        if($request->isMethod("POST")){
            $title = $request->request->get("title");
            $description = $request->request->get("description");
            $price = $request->request->get("price");
            $coverImg = $request->request->get("coverImg");
            $location = $request->request->get("location");
            $openSpots = $request->request->get("openSpots");

            $post->setTitle($title);
            $post->setDescription($description);
            $post->setPrice($price);
            $post->setCoverImg($coverImg);
            $post->setLocation($location);
            $post->setOpenSpots($openSpots);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('edit');
        }

        return $this->render('/manage_post/update.html.twig', [
            'controller_name' => 'RentController',
            'post' => $post,
        ]);
    }

}
