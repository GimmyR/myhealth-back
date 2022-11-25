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
        $resultSet = $statement->executeQuery([
                'oversightId' => $oversightId
        ]); return $resultSet->fetchAllAssociative();

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

    public function edit($entry, $details) {
        
        $entityManager = $this->getEntityManager();
        
        try {

            $entityManager->getConnection()->beginTransaction();

            $query = $entityManager->createQuery(
                'SELECT oe
                FROM App\Entity\OversightEntry oe
                WHERE oe.id = :id
                AND oe.status = 1'
            )->setParameter('id', $entry['id']);

            $result1 = $query->getResult();
            if(count($result1) != 1)
                throw new RepositoryException("Entrée introuvable !");
            $result1[0]->setDate($entry['date']);
            
            foreach($details as $detail) {

                $query = $entityManager->createQuery(
                    'SELECT ed
                    FROM App\Entity\EntryDetail ed
                    WHERE ed.id = :id
                    AND ed.status = 1'
                )->setParameter('id', $detail['id']);

                $result2 = $query->getResult();
                if(count($result2) != 1)
                    throw new RepositoryException("Détail d'une entrée introuvable !");
                $result2[0]->setValue($detail['value']);

            }
            
            $entityManager->flush();
            $entityManager->getConnection()->commit();

        } catch(Exception $e) {

            $entityManager->getConnection()->rollBack();
            throw new RepositoryException('Données impossibles à enregistrer !');

        }

    }

    public function findByIdAndAccountId(int $id, int $accountId) {

        $connection = $this->getEntityManager()->getConnection();
        $sql = 
            'SELECT oe.*
            FROM OversightEntry oe
            JOIN Oversight o
            ON oe.oversightId = o.id
            WHERE oe.id = :id
            AND o.accountId = :accountId'
        ;
        
        $statement = $connection->prepare($sql);
        $result = $statement->executeQuery([
            'id' => $id,
            'accountId' => $accountId
        ]); return $result->fetchAssociative();

    }

}

?>