<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findUpcomingReservations(User $user, \DateTime $now): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.date > :now')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->orderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPastReservations(User $user, \DateTime $now): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.date <= :now')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingReservationsByRestaurant(Restaurant $restaurant, \DateTime $now): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.restaurant = :restaurant')
            ->andWhere('r.date > :now')
            ->setParameter('restaurant', $restaurant)
            ->setParameter('now', $now)
            ->orderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPastReservationsByRestaurant(Restaurant $restaurant, \DateTime $now): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.restaurant = :restaurant')
            ->andWhere('r.date <= :now')
            ->setParameter('restaurant', $restaurant)
            ->setParameter('now', $now)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

