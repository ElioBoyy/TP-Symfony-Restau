<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Restaurant;
use App\Form\MenuType;
use App\Form\PlatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/owner/restaurant/{restaurant_id}/menu')]
#[IsGranted('ROLE_OWNER')]
class OwnerMenuController extends AbstractController
{
    #[Route('/', name: 'app_owner_menu_index', methods: ['GET'])]
    public function index(int $restaurant_id, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant) {
            throw $this->createNotFoundException('The restaurant does not exist');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $menus = $entityManager->getRepository(Menu::class)->findBy(['restaurant' => $restaurant]);

        return $this->render('owner/menu/index.html.twig', [
            'restaurant' => $restaurant,
            'menus' => $menus,
        ]);
    }

    #[Route('/new', name: 'app_owner_menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $restaurant_id, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);

        if (!$restaurant) {
            throw $this->createNotFoundException('The restaurant does not exist');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $menu = new Menu();
        $menu->setRestaurant($restaurant);
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($menu);
            $entityManager->flush();

            $this->addFlash('success', 'Le menu a été créé avec succès.');

            return $this->redirectToRoute('app_owner_menu_index', ['restaurant_id' => $restaurant->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('owner/menu/new.html.twig', [
            'restaurant' => $restaurant,
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_owner_menu_show', methods: ['GET'])]
    public function show(int $restaurant_id, int $id, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);
        $menu = $entityManager->getRepository(Menu::class)->find($id);

        if (!$restaurant || !$menu) {
            throw $this->createNotFoundException('The restaurant or menu does not exist');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('owner/menu/show.html.twig', [
            'restaurant' => $restaurant,
            'menu' => $menu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_owner_menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $restaurant_id, int $id, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);
        $menu = $entityManager->getRepository(Menu::class)->find($id);

        if (!$restaurant || !$menu) {
            throw $this->createNotFoundException('The restaurant or menu does not exist');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le menu a été modifié avec succès.');

            return $this->redirectToRoute('app_owner_menu_show', ['restaurant_id' => $restaurant->getId(), 'id' => $menu->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('owner/menu/edit.html.twig', [
            'restaurant' => $restaurant,
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_owner_menu_delete', methods: ['POST'])]
    public function delete(Request $request, int $restaurant_id, int $id, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);
        $menu = $entityManager->getRepository(Menu::class)->find($id);

        if (!$restaurant || !$menu) {
            throw $this->createNotFoundException('The restaurant or menu does not exist');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($menu);
            $entityManager->flush();
            $this->addFlash('success', 'Le menu a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_owner_menu_index', ['restaurant_id' => $restaurant->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/plat/new', name: 'app_owner_plat_new', methods: ['GET', 'POST'])]
    public function newPlat(Request $request, int $restaurant_id, int $id, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);
        $menu = $entityManager->getRepository(Menu::class)->find($id);

        if (!$restaurant || !$menu) {
            throw $this->createNotFoundException('The restaurant or menu does not exist');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $plat = new Plat();
        $plat->setMenu($menu);
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($plat);
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été ajouté avec succès.');

            return $this->redirectToRoute('app_owner_menu_show', ['restaurant_id' => $restaurant->getId(), 'id' => $menu->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('owner/plat/new.html.twig', [
            'restaurant' => $restaurant,
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{menu_id}/plat/{id}/edit', name: 'app_owner_plat_edit', methods: ['GET', 'POST'])]
    public function editPlat(Request $request, int $restaurant_id, int $menu_id, int $id, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);
        $menu = $entityManager->getRepository(Menu::class)->find($menu_id);
        $plat = $entityManager->getRepository(Plat::class)->find($id);

        if (!$restaurant || !$menu || !$plat) {
            throw $this->createNotFoundException('The restaurant, menu or dish does not exist');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été modifié avec succès.');

            return $this->redirectToRoute('app_owner_menu_show', ['restaurant_id' => $restaurant->getId(), 'id' => $menu->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('owner/plat/edit.html.twig', [
            'restaurant' => $restaurant,
            'menu' => $menu,
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    #[Route('/{menu_id}/plat/{id}', name: 'app_owner_plat_delete', methods: ['POST'])]
    public function deletePlat(Request $request, int $restaurant_id, int $menu_id, int $id, EntityManagerInterface $entityManager): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant_id);
        $menu = $entityManager->getRepository(Menu::class)->find($menu_id);
        $plat = $entityManager->getRepository(Plat::class)->find($id);

        if (!$restaurant || !$menu || !$plat) {
            throw $this->createNotFoundException('The restaurant, menu or dish does not exist');
        }

        if ($restaurant->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$plat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($plat);
            $entityManager->flush();
            $this->addFlash('success', 'Le plat a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_owner_menu_show', ['restaurant_id' => $restaurant->getId(), 'id' => $menu->getId()], Response::HTTP_SEE_OTHER);
    }
}

