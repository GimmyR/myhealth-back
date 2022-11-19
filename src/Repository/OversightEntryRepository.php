<?php

namespace App\Repository;

use App\Entity\OversightEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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

    public function add(OversightEntry $entry, array $details) {
        
        $entityManager = $this->getEntityManager();
        
        try {

            $entityManager->getConnection()->beginTransaction();

            $entityManager->persist($entry);
            $entityManager->flush();
            $query = $entityManager->createQuery(
                'SELECT oe
                FROM App\Entity\OversightEntry oe
                WHERE oe.oversightId = :oversightId
                AND oe.date = :date'
            )->setParameters(
                [
                    'oversightId' => $entry->getOversightId(),
                    'date' => $entry->getDate()
                ]
            ); $result = $query->getResult();

            if(count($result) != 1)
                throw new Exception();
            
            foreach($details as $detail) {
                $detail->setEntryId($result[0]->getId());
                $entityManager->persist($detail);
                $entityManager->flush();
            } $entityManager->getConnection()->commit();

        } catch(Exception $e) {

            $entityManager->getConnection()->rollBack();
            throw new RepositoryException('Données impossibles à enregistrer !');

        }

    }

}

?>