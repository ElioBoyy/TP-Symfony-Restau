<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use App\Service\RestaurantSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
    #[Route('/restaurants/search', name: 'restaurant_search', methods: ['GET'])]
    public function search(Request $request, RestaurantSearchService $searchService): Response
    {
        $query = $request->query->get('q');
        $page = $request->query->getInt('page', 1);
        $limit = 6;

        if ($query) {
            $restaurants = $searchService->searchRestaurants($query, $page, $limit);
            $total = $searchService->getTotalResults($query);
        } else {
            $restaurants = [];
            $total = 0;
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('restaurant/_search_results.html.twig', [
                'restaurants' => $restaurants
            ]);
        }

        return $this->render('restaurant/search.html.twig', [
            'restaurants' => $restaurants,
            'query' => $query,
            'page' => $page,
            'limit' => $limit,
            'total' => $total
        ]);
    }

    #[Route('/restaurants', name: 'restaurant_list', methods: ['GET'])]
    public function list(RestaurantRepository $restaurantRepository): Response
    {
        $restaurants = $restaurantRepository->findAll();

        return $this->render('restaurant/list.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    #[Route('/restaurant/{id}', name: 'restaurant_details', methods: ['GET'])]
    public function details(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/details.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }
}

