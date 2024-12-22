<?php

namespace App\Controller;

use App\Service\RestaurantSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
    private $restaurantSearchService;

    public function __construct(RestaurantSearchService $restaurantSearchService)
    {
        $this->restaurantSearchService = $restaurantSearchService;
    }

    #[Route('/restaurants/search', name: 'restaurant_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $query = $request->query->get('q', '');
        $page = $request->query->getInt('page', 1);
        $limit = 6;

        $restaurants = $this->restaurantSearchService->searchRestaurants($query, $page, $limit);
        $total = $this->restaurantSearchService->getTotalResults($query);

        if ($request->isXmlHttpRequest()) {
            return $this->render('restaurant/_search_results.html.twig', [
                'restaurants' => $restaurants,
                'query' => $query,
            ]);
        }

        return $this->render('restaurant/search.html.twig', [
            'restaurants' => $restaurants,
            'query' => $query,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }

    #[Route('/restaurant/{id}', name: 'restaurant_details', methods: ['GET'])]
    public function details(int $id): Response
    {
        // Implement the logic to fetch and display restaurant details
        // This is just a placeholder
        return $this->render('restaurant/details.html.twig', [
            'id' => $id,
        ]);
    }
}

