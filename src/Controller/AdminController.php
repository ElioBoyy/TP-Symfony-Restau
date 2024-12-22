<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Restaurant;
use App\Entity\Reservation;
use App\Repository\UserRepository;
use App\Repository\RestaurantRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/users', name: 'admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/change-role', name: 'admin_change_user_role', methods: ['POST'])]
    public function changeUserRole(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('change_role'.$user->getId(), $request->request->get('_token'))) {
            $newRole = $user->getRoles()[0] === 'ROLE_USER' ? ['ROLE_OWNER'] : ['ROLE_USER'];
            $user->setRoles($newRole);
            $entityManager->flush();
            $this->addFlash('success', 'Le rôle de l\'utilisateur a été modifié avec succès.');
        }

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/user/{id}/delete', name: 'admin_delete_user', methods: ['POST'])]
    public function deleteUser(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/restaurants', name: 'admin_restaurants')]
    public function restaurants(RestaurantRepository $restaurantRepository): Response
    {
        $restaurants = $restaurantRepository->findAll();
        return $this->render('admin/restaurants.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    #[Route('/restaurant/{id}/delete', name: 'admin_delete_restaurant', methods: ['POST'])]
    public function deleteRestaurant(Restaurant $restaurant, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($restaurant);
            $entityManager->flush();
            $this->addFlash('success', 'Le restaurant a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_restaurants');
    }

    #[Route('/reservations', name: 'admin_reservations')]
    public function reservations(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findAll();
        return $this->render('admin/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }
}

