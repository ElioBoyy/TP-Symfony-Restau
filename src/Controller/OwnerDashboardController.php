<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OwnerDashboardController extends AbstractController
{
    #[Route('/owner/dashboard', name: 'app_owner_dashboard')]
    #[IsGranted('ROLE_OWNER')]
    public function index(RestaurantRepository $restaurantRepository, ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        $restaurants = $restaurantRepository->findBy(['owner' => $user]);
        $now = new \DateTime();

        $dashboardData = [];

        foreach ($restaurants as $restaurant) {
            $upcomingReservations = $reservationRepository->findUpcomingReservationsByRestaurant($restaurant, $now);
            $pastReservations = $reservationRepository->findPastReservationsByRestaurant($restaurant, $now);

            $dashboardData[] = [
                'restaurant' => $restaurant,
                'upcomingReservations' => $upcomingReservations,
                'pastReservations' => $pastReservations,
            ];
        }

        return $this->render('owner_dashboard/index.html.twig', [
            'dashboardData' => $dashboardData,
        ]);
    }
}

