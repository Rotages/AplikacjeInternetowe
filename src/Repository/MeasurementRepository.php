<?php

namespace App\Repository;

use App\Entity\Measurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Measurement::class);
    }

    // Existing methods...

    public function findByCity(string $city): array
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.location', 'l') // Assuming there's a relation to Location
            ->where('l.city = :city')
            ->setParameter('city', $city)
            ->getQuery()
            ->getResult();
    }
}