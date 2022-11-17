<?php

namespace App\Repository;

use App\Entity\Oversight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OversightRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {

        parent::__construct($registry, Oversight::class);
        
    }

    public function findByIdAndAccountId(int $id, int $accountId) {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT o
            FROM App\Entity\Oversight o
            WHERE o.id = :id
            AND o.accountId = :accountId'
        )->setParameters(
            [
                'id' => $id,
                'accountId' => $accountId
            ]
        ); $result = $query->getResult();

        if(count($result) == 1)
            return $result[0];
        else
            throw new RepositoryException("Vous n'êtes pas associé pas à cette surveillance !");

    }

}

?>