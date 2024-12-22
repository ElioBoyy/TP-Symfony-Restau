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

#[Route('/reservation')]
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
        $tables = $plan->getTables();
        $wallpoints = $plan->getWallpoints();

        $tableData = [];
        foreach ($tables as $table) {
            $tableData[] = [
                'id' => $table->getId(),
                'tableNumber' => $table->getTableNumber(),
                'positionX' => $table->getPositionX(),
                'positionY' => $table->getPositionY(),
                'nbPersonneMax' => $table->getNbPersonneMax(),
            ];
        }

        $wallpointData = [];
        foreach ($wallpoints as $wallpoint) {
            $wallpointData[] = [
                'id' => $wallpoint->getId(),
                'positionX' => $wallpoint->getPositionX(),
                'positionY' => $wallpoint->getPositionY(),
            ];
        }

        return $this->render('reservation/select_table.html.twig', [
            'plan' => $plan,
            'tables' => $tableData,
            'wallpoints' => $wallpointData,
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
        $reservation->addTable($table);
        $reservation->setDate($date);
        $reservation->setNbPersonne($table->getNbPersonneMax()); // Par défaut, on réserve pour le nombre max de personnes
        $reservation->setIsActive(true);

        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été effectuée avec succès.');

        return $this->redirectToRoute('restaurant_details', ['id' => $table->getPlan()->getRestaurant()->getId()]);
    }
}

