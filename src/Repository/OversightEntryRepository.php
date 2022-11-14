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

        $connection = $this->getEntityManager()->getConnection();

        $sql = 
            'SELECT oe.*
            FROM OversightEntry oe
            WHERE oe.oversightId = :oversightId
            AND oe.status = 1'
        ;

        $statement = $connection->prepare($sql);
        $resultSet = $statement->executeQuery(
            [
                'oversightId' => $oversightId
            ]
        ); return $resultSet->fetchAllAssociative();

    }

}

?>