<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/owner/restaurant')]
//#[IsGranted('ROLE_OWNER')]
class OwnerRestaurantController extends AbstractController
{
    #[Route('/', name: 'app_owner_restaurant_index', methods: ['GET'])]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        return $this->render('owner/restaurant/index.html.twig', [
            'restaurants' => $restaurantRepository->findBy(['owner' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'app_owner_restaurant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $restaurant = new Restaurant();
        $restaurant->setOwner($this->getUser());
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($restaurant);
            $entityManager->flush();

            $this->addFlash('success', 'Le restaurant a été créé avec succès.');

            return $this->redirectToRoute('app_owner_restaurant_index');
        }

        return $this->render('owner/restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_owner_restaurant_show', methods: ['GET'])]
    public function show(Restaurant $restaurant): Response
    {
        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('owner/restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_owner_restaurant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le restaurant a été modifié avec succès.');

            return $this->redirectToRoute('app_owner_restaurant_show', ['id' => $restaurant->getId()]);
        }

        return $this->render('owner/restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }
}

