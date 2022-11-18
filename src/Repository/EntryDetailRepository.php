<?php

namespace App\Repository;

use App\Entity\EntryDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EntryDetailRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {

        parent::__construct($registry, EntryDetail::class);
        
    }

    public function findAllByParameterId(int $parameterId) {

        $connection = $this->getEntityManager()->getConnection();

        $sql = 
            'SELECT DATE_FORMAT(oe.date, "%d %b %y") as date, ed.*
            FROM EntryDetail ed
            JOIN Parameter p
            ON p.id = ed.parameterId
            JOIN OversightEntry oe
            ON oe.id = ed.entryId
            WHERE ed.parameterId = :parameterId
            AND ed.status = 1
            AND oe.status = 1
            ORDER BY oe.date ASC';

        $statement = $connection->prepare($sql);
        $resultSet = $statement->executeQuery([ 
            'parameterId' => $parameterId
        ]);

        return $resultSet->fetchAllAssociative();

    }

    public function findAllByEntryId(int $entryId) {

        $connection = $this->getEntityManager()->getConnection();

        $sql = 
            'SELECT ed.*
            FROM EntryDetail ed
            JOIN Parameter p
            ON p.id = ed.parameterId
            JOIN OversightEntry oe
            ON oe.id = ed.entryId
            WHERE ed.entryId = :entryId
            AND ed.status = 1
            AND p.status = 1
            ORDER BY oe.date ASC, ed.parameterId ASC';

        $statement = $connection->prepare($sql);
        $resultSet = $statement->executeQuery([ 
            'entryId' => $entryId
        ]);

        return $resultSet->fetchAllAssociative();

    }

}

?>