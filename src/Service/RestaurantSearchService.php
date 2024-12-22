<?php

namespace App\Service;

use App\Repository\RestaurantRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class RestaurantSearchService
{
    private $restaurantRepository;

    public function __construct(RestaurantRepository $restaurantRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
    }

    public function searchRestaurants(string $query, int $page = 1, int $limit = 6): array
    {
        $allRestaurants = $this->restaurantRepository->findAll();
        $filteredRestaurants = [];

        foreach ($allRestaurants as $restaurant) {
            $similarity = $this->calculateSimilarity($query, $restaurant->getName());
            if ($similarity > 50) {  // Adjust this threshold as needed
                $filteredRestaurants[] = [
                    'restaurant' => $restaurant,
                    'similarity' => $similarity
                ];
            }
        }

        usort($filteredRestaurants, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $offset = ($page - 1) * $limit;
        $paginatedResults = array_slice($filteredRestaurants, $offset, $limit);

        return array_map(function($item) {
            return $item['restaurant'];
        }, $paginatedResults);
    }

    public function getTotalResults(string $query): int
    {
        $allRestaurants = $this->restaurantRepository->findAll();
        $count = 0;

        foreach ($allRestaurants as $restaurant) {
            $similarity = $this->calculateSimilarity($query, $restaurant->getName());
            if ($similarity > 50) { 
                $count++;
            }
        }

        return $count;
    }

    private function calculateSimilarity(string $str1, string $str2): float
    {
        $str1 = $this->normalizeString($str1);
        $str2 = $this->normalizeString($str2);

        similar_text($str1, $str2, $percent);
        return $percent;
    }

    private function normalizeString(string $str): string
    {
        $str = strtolower($str);
        $str = preg_replace('/\s+/', '', $str);
        $str = str_replace(['resto', 'restaurant'], '', $str);
        return $str;
    }
}

