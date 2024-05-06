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
        $isAdmin = $session->get('admin');

        if ($isAdmin) {
            $apart = $apartRepository->find($id);
            if (!$apart) {
                $this->addFlash('error', 'The post does not exist');
                return $this->redirectToRoute('app_manage_post');
            }
        } else {
            $apart = $apartRepository->findByIdAndEmail($id, $email);
            if (!$apart) {
                $this->addFlash('error', 'The post does not exist or you are not allowed to delete it');
                return $this->redirectToRoute('app_manage_post');
            }
        }
        $entityManager = $doctrine->getManager();

        if (!$entityManager->contains($apart)) {
            throw new \Exception('The entity is not managed by Doctrine');
        }
        $entityManager->remove($apart);
        $entityManager->flush();
        $this->addFlash('success', 'Post deleted successfully.');
        if ($isAdmin && $email == $apart->getMail()) {
            return $this->redirectToRoute('app_manage_post');
        } else {
            if  ($isAdmin && $email != $apart->getMail())
            {
                return $this->redirectToRoute('managePost');

            } else {
                return $this->redirectToRoute('app_manage_post');
            }
        }
    }

    #[Route('/edit/update/{id}', name: 'update_post')]
    public function update(Request $request, EntityManagerInterface $entityManager, ApartRepository $apartRepository, $id): Response
    {
        $session = $request->getSession();
        $email = $session->get('email');
        $isAdmin = $session->get('admin');

        $post = $apartRepository->findByIdAndEmail($id, $email);

        if (!$post) {
            $this->addFlash('error', 'The post does not exist or you are not allowed to edit it');
            return $this->redirectToRoute('app_manage_post');
        }

        if (!$isAdmin && $post->getEmail() !== $email) {
            $this->addFlash('error', 'You are not allowed to edit this post');
            return $this->redirectToRoute('app_manage_post');
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

            $this->addFlash('success', 'Post updated successfully.');
            return $this->redirectToRoute('edit');
        }

        return $this->render('/manage_post/update.html.twig', [
            'controller_name' => 'RentController',
            'post' => $post,
        ]);
    }
    #[Route('/edit/add-spot/{id}', name: 'add_spot')]
    public function addSpot(Request $request, EntityManagerInterface $entityManager, ApartRepository $apartRepository, $id): Response
    {
        $post = $apartRepository->find($id);

        if (!$post) {
            $this->addFlash('error', 'The post does not exist');
            return $this->redirectToRoute('app_manage_post');
        }

        $post->setOpenSpots($post->getOpenSpots() + 1);

        $entityManager->persist($post);
        $entityManager->flush();

        $this->addFlash('success', 'Spot added successfully.');
        return $this->redirectToRoute('app_manage_post');
    }

    #[Route('/edit/remove-spot/{id}', name: 'remove_spot')]
    public function removeSpot(Request $request, EntityManagerInterface $entityManager, ApartRepository $apartRepository, $id): Response
    {
        $post = $apartRepository->find($id);

        if (!$post) {
            $this->addFlash('error', 'The post does not exist');
            return $this->redirectToRoute('app_manage_post');
        }

        if ($post->getOpenSpots() > 0) {

            $post->setOpenSpots($post->getOpenSpots() - 1);

            $entityManager->persist($post);
            $entityManager->flush();


            $this->addFlash('success', 'Spot removed successfully.');
        } else {
            $this->addFlash('error', 'No spots available to remove.');
        }

        return $this->redirectToRoute('app_manage_post');
    }


}
