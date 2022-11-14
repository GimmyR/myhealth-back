<?php

namespace App\Repository;

use App\Entity\Oversight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OversightRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {

        parent::__construct($registry, Oversight::class);
        
    }

    public function findById(int $id) {

        $connection = $this->getEntityManager()->getConnection();

        $sql = 
            "SELECT o.* 
            FROM Oversight o 
            WHERE o.id = :id
            AND o.status = 1";

        $statement = $connection->prepare($sql);
        $resultSet = $statement->executeQuery(
            [
                'id' => $id
            ]
        );
        $result = $resultSet->fetchAssociative();

        if($result != false)
            return $result;
        else
            return null;

    }

}

?>