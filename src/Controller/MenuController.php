<?php

namespace App\Controller;

use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/restaurant/{id}/menus', name: 'restaurant_menus')]
    public function index(Restaurant $restaurant): Response
    {
        return $this->render('menu/index.html.twig', [
            'restaurant' => $restaurant,
            'menus' => $restaurant->getMenus()->filter(function($menu) {
                return $menu->isActive();
            }),
        ]);
    }
}

