<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Form\PlanType;
use App\Repository\PlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/owner/room')]
#[IsGranted('ROLE_OWNER')]
class OwnerRoomController extends AbstractController
{
    #[Route('/', name: 'app_owner_room_index', methods: ['GET'])]
    public function index(PlanRepository $planRepository): Response
    {
        return $this->render('owner/room/index.html.twig', [
            'plans' => $planRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_owner_room_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $plan = new Plan();
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($plan);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle salle créée avec succès.');

            return $this->redirectToRoute('app_owner_room_index');
        }

        return $this->render('owner/room/new.html.twig', [
            'plan' => $plan,
            'form' => $form->createView(),
        ]);
    }

    // Autres méthodes du contrôleur...
}

