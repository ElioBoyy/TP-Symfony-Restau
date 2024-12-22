<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Entity\Restaurant;
use App\Form\PlanType;
use App\Repository\PlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/owner/restaurant/{restaurant_id}/room')]
#[IsGranted('ROLE_OWNER')]
class OwnerRoomController extends AbstractController
{
    #[Route('/', name: 'app_owner_room_index', methods: ['GET'])]
    public function index(int $restaurant_id, PlanRepository $planRepository, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant non trouvé.');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce restaurant.');
        }

        $plans = array_filter($planRepository->findBy(['restaurant' => $restaurant]), function($plan) {
            return $plan->isActive();
        });

        return $this->render('owner/room/index.html.twig', [
            'plans' => $plans,
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/new', name: 'app_owner_room_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $restaurant_id, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant non trouvé.');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce restaurant.');
        }

        $plan = new Plan();
        $plan->setRestaurant($restaurant);
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($plan);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle salle créée avec succès.');

            return $this->redirectToRoute('app_owner_room_index', ['restaurant_id' => $restaurant_id]);
        }

        return $this->render('owner/room/new.html.twig', [
            'plan' => $plan,
            'form' => $form->createView(),
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/{id}', name: 'app_owner_room_show', methods: ['GET'])]
    public function show(int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant non trouvé.');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce restaurant.');
        }

        if ($plan->getRestaurant() !== $restaurant) {
            throw $this->createNotFoundException('Cette salle n\'appartient pas à ce restaurant.');
        }

        return $this->render('owner/room/show.html.twig', [
            'plan' => $plan,
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_owner_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant non trouvé.');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce restaurant.');
        }

        if ($plan->getRestaurant() !== $restaurant) {
            throw $this->createNotFoundException('Cette salle n\'appartient pas à ce restaurant.');
        }

        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La salle a été modifiée avec succès.');

            return $this->redirectToRoute('app_owner_room_show', ['restaurant_id' => $restaurant_id, 'id' => $plan->getId()]);
        }

        return $this->render('owner/room/edit.html.twig', [
            'plan' => $plan,
            'form' => $form->createView(),
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/{id}', name: 'app_owner_room_delete', methods: ['POST'])]
    public function delete(Request $request, int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant non trouvé.');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce restaurant.');
        }

        if ($plan->getRestaurant() !== $restaurant) {
            throw $this->createNotFoundException('Cette salle n\'appartient pas à ce restaurant.');
        }

        if ($this->isCsrfTokenValid('delete'.$plan->getId(), $request->request->get('_token'))) {
            foreach ($plan->getTables() as $table) {
                $table->setIsActive(false);
                $entityManager->persist($table);
            }
            $plan->setIsActive(false);
            $entityManager->persist($plan);
            $entityManager->flush();
            $this->addFlash('success', 'La salle et ses tables ont été désactivées avec succès.');
        }

        return $this->redirectToRoute('app_owner_room_index', ['restaurant_id' => $restaurant_id]);
    }
}

