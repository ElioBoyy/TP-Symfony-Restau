<?php

namespace App\Service;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;

class RestaurantSearchService
{
    private $restaurantRepository;

    public function __construct(RestaurantRepository $restaurantRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
    }

    public function searchRestaurants(string $query): array
    {
        $allRestaurants = $this->restaurantRepository->findAll();
        $searchResults = [];

        foreach ($allRestaurants as $restaurant) {
            $distance = levenshtein(strtolower($query), strtolower($restaurant->getName()));
            $searchResults[] = [
                'restaurant' => $restaurant,
                'distance' => $distance,
            ];
        }

        usort($searchResults, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return array_map(function ($result) {
            return $result['restaurant'];
        }, $searchResults);
    }
}

