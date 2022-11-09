<?php

namespace App\Repository;

use App\Entity\OversightEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OversightEntryRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {

        parent::__construct($registry, OversightEntry::class);
        
    }

    public function findAllByOversightId(int $oversightId) {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT o
            FROM App\Entity\OversightEntry o
            WHERE o.oversightId = :oversightId
            AND o.status = 1
            ORDER BY o.date ASC'
        )->setParameter('oversightId', $oversightId);

        return $query->getResult();

    }

}

?>