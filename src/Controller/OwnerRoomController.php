<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Entity\Restaurant;
use App\Entity\Wallpoint;
use App\Entity\Table;
use App\Form\PlanType;
use App\Form\WallpointType;
use App\Form\TableType;
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

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce restaurant.');
        }

        $plans = $planRepository->findBy(['restaurant' => $restaurant, 'isActive' => true]);

        return $this->render('owner/room/index.html.twig', [
            'plans' => $plans,
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/new', name: 'app_owner_room_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $restaurant_id, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce restaurant.');
        }

        $plan = new Plan();
        $plan->setRestaurant($restaurant);
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($plan);
            $entityManager->flush();

            return $this->redirectToRoute('app_owner_room_show', ['restaurant_id' => $restaurant_id, 'id' => $plan->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('owner/room/new.html.twig', [
            'plan' => $plan,
            'form' => $form,
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/{id}', name: 'app_owner_room_show', methods: ['GET'])]
    public function show(int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser() || $plan->getRestaurant() !== $restaurant) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette salle.');
        }

        $wallpoints = $entityManager->getRepository(Wallpoint::class)->findBy(['plan' => $plan]);
        $tables = $entityManager->getRepository(Table::class)->findBy(['plan' => $plan]);

        $serializedWallpoints = array_map(function($wp) {
            return [
                'id' => $wp->getId(),
                'positionX' => $wp->getPositionX(),
                'positionY' => $wp->getPositionY()
            ];
        }, $wallpoints);

        $serializedTables = array_map(function($table) {
            return [
                'id' => $table->getId(),
                'positionX' => $table->getPositionX(),
                'positionY' => $table->getPositionY(),
                'nbPersonneMax' => $table->getNbPersonneMax(),
                'tableNumber' => $table->getTableNumber()
            ];
        }, $tables);

        return $this->render('owner/room/show.html.twig', [
            'plan' => $plan,
            'restaurant' => $restaurant,
            'wallpoints' => $serializedWallpoints,
            'tables' => $serializedTables,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_owner_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser() || $plan->getRestaurant() !== $restaurant) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette salle.');
        }

        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_owner_room_show', ['restaurant_id' => $restaurant_id, 'id' => $plan->getId()], Response::HTTP_SEE_OTHER);
        }

        $wallpoints = $entityManager->getRepository(Wallpoint::class)->findBy(['plan' => $plan]);
        $tables = $entityManager->getRepository(Table::class)->findBy(['plan' => $plan]);

        $serializedWallpoints = array_map(function($wp) {
            return [
                'id' => $wp->getId(),
                'positionX' => $wp->getPositionX(),
                'positionY' => $wp->getPositionY()
            ];
        }, $wallpoints);

        $serializedTables = array_map(function($table) {
            return [
                'id' => $table->getId(),
                'positionX' => $table->getPositionX(),
                'positionY' => $table->getPositionY(),
                'nbPersonneMax' => $table->getNbPersonneMax(),
                'tableNumber' => $table->getTableNumber()
            ];
        }, $tables);

        $wallpointForm = $this->createForm(WallpointType::class);
        $tableForm = $this->createForm(TableType::class);

        return $this->render('owner/room/edit.html.twig', [
            'plan' => $plan,
            'form' => $form,
            'restaurant' => $restaurant,
            'wallpoints' => $serializedWallpoints,
            'tables' => $serializedTables,
            'wallpointForm' => $wallpointForm,
            'tableForm' => $tableForm,
        ]);
    }

    #[Route('/{id}', name: 'app_owner_room_delete', methods: ['POST'])]
    public function delete(Request $request, int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser() || $plan->getRestaurant() !== $restaurant) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette salle.');
        }

        if ($this->isCsrfTokenValid('delete'.$plan->getId(), $request->request->get('_token'))) {
            $entityManager->remove($plan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_owner_room_index', ['restaurant_id' => $restaurant_id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/wallpoint/new', name: 'app_owner_room_wallpoint_new', methods: ['POST'])]
    public function newWallpoint(Request $request, int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $wallpoint = new Wallpoint();
        $form = $this->createForm(WallpointType::class, $wallpoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wallpoint->setPlan($plan);
            $entityManager->persist($wallpoint);
            $entityManager->flush();

            return $this->json(['success' => true, 'id' => $wallpoint->getId()]);
        }

        return $this->json(['success' => false, 'errors' => (string) $form->getErrors(true, false)]);
    }

    #[Route('/{id}/table/new', name: 'app_owner_room_table_new', methods: ['POST'])]
    public function newTable(Request $request, int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $table = new Table();
        $form = $this->createForm(TableType::class, $table);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $table->setPlan($plan);
            $entityManager->persist($table);
            $entityManager->flush();

            return $this->json(['success' => true, 'id' => $table->getId()]);
        }

        return $this->json(['success' => false, 'errors' => (string) $form->getErrors(true, false)]);
    }

    #[Route('/wallpoint/{id}/edit', name: 'app_owner_room_wallpoint_edit', methods: ['POST'])]
    public function editWallpoint(Request $request, int $restaurant_id, Wallpoint $wallpoint, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WallpointType::class, $wallpoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false, 'errors' => (string) $form->getErrors(true, false)]);
    }

    #[Route('/table/{id}/edit', name: 'app_owner_room_table_edit', methods: ['POST'])]
    public function editTable(Request $request, int $restaurant_id, Table $table, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser() || $table->getPlan()->getRestaurant() !== $restaurant) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette table.');
        }

        $form = $this->createForm(TableType::class, $table);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->json(['success' => true]);
        }

        // Handle table number update
        $newTableNumber = $request->request->get('tableNumber');
        if ($newTableNumber !== null) {
            $table->setTableNumber($newTableNumber);
            $entityManager->flush();
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false, 'errors' => (string) $form->getErrors(true, false)]);
    }

    #[Route('/wallpoint/{id}/delete', name: 'app_owner_room_wallpoint_delete', methods: ['POST'])]
    public function deleteWallpoint(Request $request, int $restaurant_id, Wallpoint $wallpoint, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wallpoint->getId(), $request->request->get('_token'))) {
            $entityManager->remove($wallpoint);
            $entityManager->flush();
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false, 'message' => 'Invalid CSRF token']);
    }

    #[Route('/table/{id}/delete', name: 'app_owner_room_table_delete', methods: ['POST'])]
    public function deleteTable(Request $request, int $restaurant_id, Table $table, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser() || $table->getPlan()->getRestaurant() !== $restaurant) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette table.');
        }

        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $entityManager->remove($table);
            $entityManager->flush();
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false, 'message' => 'Invalid CSRF token']);
    }

    #[Route('/{id}/wallpoint/auto-new', name: 'app_owner_room_wallpoint_auto_new', methods: ['POST'])]
    public function newAutoWallpoint(Request $request, int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser() || $plan->getRestaurant() !== $restaurant) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette salle.');
        }

        $wallpoint = new Wallpoint();
        $wallpoint->setPlan($plan);
        $wallpoint->setPositionX($request->request->get('positionX'));
        $wallpoint->setPositionY($request->request->get('positionY'));

        $entityManager->persist($wallpoint);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'id' => $wallpoint->getId()
        ]);
    }

    #[Route('/{id}/wallpoint/center-new', name: 'app_owner_room_wallpoint_center_new', methods: ['POST'])]
    public function newCenterWallpoint(Request $request, int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser() || $plan->getRestaurant() !== $restaurant) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette salle.');
        }

        $wallpoint = new Wallpoint();
        $wallpoint->setPlan($plan);
        $wallpoint->setPositionX($request->request->get('positionX'));
        $wallpoint->setPositionY($request->request->get('positionY'));

        $entityManager->persist($wallpoint);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'id' => $wallpoint->getId()
        ]);
    }

    #[Route('/{id}/table/center-new', name: 'app_owner_room_table_center_new', methods: ['POST'])]
    public function newCenterTable(Request $request, int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser() || $plan->getRestaurant() !== $restaurant) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette salle.');
        }

        $tableNumber = $this->getNextTableNumber($plan);

        $table = new Table();
        $table->setPlan($plan);
        $table->setPositionX($request->request->get('positionX'));
        $table->setPositionY($request->request->get('positionY'));
        $table->setNbPersonneMax(4); // Valeur par défaut
        $table->setTableNumber($tableNumber);

        $entityManager->persist($table);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'id' => $table->getId(),
            'nbPersonneMax' => $table->getNbPersonneMax(),
            'tableNumber' => $table->getTableNumber()
        ]);
    }

    #[Route('/{id}/save-layout', name: 'app_owner_room_save_layout', methods: ['POST'])]
    public function saveLayout(Request $request, int $restaurant_id, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant || $restaurant->getOwner() !== $this->getUser() || $plan->getRestaurant() !== $restaurant) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette salle.');
        }

        $wallpointsData = json_decode($request->request->get('wallpoints'), true);
        $tablesData = json_decode($request->request->get('tables'), true);
        $deletedWallpoints = json_decode($request->request->get('deletedWallpoints'), true);
        $deletedTables = json_decode($request->request->get('deletedTables'), true);

        // Update wallpoints
        foreach ($wallpointsData as $wpData) {
            $wallpoint = $entityManager->getRepository(Wallpoint::class)->find($wpData['id']);
            if ($wallpoint && $wallpoint->getPlan() === $plan) {
                $wallpoint->setPositionX($wpData['positionX']);
                $wallpoint->setPositionY($wpData['positionY']);
            }
        }

        // Update tables
        foreach ($tablesData as $tableData) {
            $table = $entityManager->getRepository(Table::class)->find($tableData['id']);
            if ($table && $table->getPlan() === $plan) {
                $table->setPositionX($tableData['positionX']);
                $table->setPositionY($tableData['positionY']);
            }
        }

        // Delete wallpoints
        foreach ($deletedWallpoints as $wpId) {
            $wallpoint = $entityManager->getRepository(Wallpoint::class)->find($wpId);
            if ($wallpoint && $wallpoint->getPlan() === $plan) {
                $entityManager->remove($wallpoint);
            }
        }

        // Delete tables
        foreach ($deletedTables as $tableId) {
            $table = $entityManager->getRepository(Table::class)->find($tableId);
            if ($table && $table->getPlan() === $plan) {
                $entityManager->remove($table);
            }
        }

        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    private function getNextTableNumber(Plan $plan): string
    {
        $tables = $plan->getTables();
        $maxNumber = 0;

        foreach ($tables as $table) {
            $currentNumber = intval(substr($table->getTableNumber(), 5));
            if ($currentNumber > $maxNumber) {
                $maxNumber = $currentNumber;
            }
        }

        return 'T' . ($maxNumber + 1);
    }
}

