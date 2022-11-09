<?php

namespace App\Repository;

use App\Entity\Parameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ParameterRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {

        parent::__construct($registry, Parameter::class);
        
    }

    public function findAllByOversightId(int $oversightId) {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Parameter p
            WHERE p.oversightId = :oversightId
            AND p.status = 1
            ORDER BY p.id ASC'
        )->setParameter('oversightId', $oversightId);

        return $query->getResult();

    }

}

?>