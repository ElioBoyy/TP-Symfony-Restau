<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\Plan;
use App\Entity\Table;
use App\Entity\Reservation;
use App\Repository\PlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservation')]
#[IsGranted('ROLE_USER')]
class ReservationController extends AbstractController
{
    #[Route('/{id}/plans', name: 'app_reservation_plans', methods: ['GET'])]
    public function selectPlan(Restaurant $restaurant, PlanRepository $planRepository): Response
    {
        $plans = $planRepository->findBy(['restaurant' => $restaurant, 'isActive' => true]);

        return $this->render('reservation/select_plan.html.twig', [
            'restaurant' => $restaurant,
            'plans' => $plans,
        ]);
    }

    #[Route('/{id}/tables', name: 'app_reservation_tables', methods: ['GET'])]
    public function selectTable(Plan $plan): Response
    {
        return $this->render('reservation/select_table.html.twig', [
            'plan' => $plan,
        ]);
    }

    #[Route('/table/{id}/reserve', name: 'app_reservation_create', methods: ['POST'])]
    public function createReservation(Request $request, Table $table, EntityManagerInterface $entityManager): Response
    {
        $time = $request->request->get('time');
        $date = new \DateTime($request->request->get('date') . ' ' . $time);

        $reservation = new Reservation();
        $reservation->setUser($this->getUser());
        $reservation->setRestaurant($table->getPlan()->getRestaurant());
        $reservation->setPlan($table->getPlan());
        $reservation->setTable($table);
        $reservation->setDate($date);
        $reservation->setNbPersonne($table->getNbPersonneMax()); // Par défaut, on réserve pour le nombre max de personnes
        $reservation->setIsActive(true);

        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été effectuée avec succès.');

        return $this->redirectToRoute('restaurant_details', ['id' => $table->getPlan()->getRestaurant()->getId()]);
    }
}

