<?php

namespace App\Repository;

use App\Entity\Oversight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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

    public function findAllByAccountId(int $accountId) {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT o
            FROM App\Entity\Oversight o
            WHERE o.accountId = :accountId'
        )->setParameter('accountId', $accountId);

        return $query->getResult();

    }

    public function create(Oversight $oversight, array $parameters) {

        $entityManager = $this->getEntityManager();

        try {

            $entityManager->getConnection()->beginTransaction();

            $entityManager->persist($oversight);
            $entityManager->flush();
            $oversight = $this->findByAccountIdAndDateAndTitle(
                $entityManager, 
                $oversight->getAccountId(),
                $oversight->getDate(),
                $oversight->getTitle()
            ); $this->createParameters(
                $entityManager, 
                $oversight->getId(), 
                $parameters
            );
            
            $entityManager->getConnection()->commit();

        } catch(Exception $e) {

            $entityManager->getConnection()->rollBack();
            throw new RepositoryException("Impossible de créer votre surveillance !");

        }

    }

    public function createParameters(EntityManager $entityManager, 
                                        string $oversightId, 
                                        array $parameters) {

        foreach($parameters as $parameter) {
            $parameter->setOversightId($oversightId);
            $entityManager->persist($parameter);
        } $entityManager->flush();

    }

    public function findByAccountIdAndDateAndTitle(EntityManager $entityManager, string $accountId, string $date, string $title) {

        $query = $entityManager->createQuery(
            "SELECT o
            FROM App\Entity\Oversight o
            WHERE o.accountId = :accountId
            AND o.date = :date
            AND o.title = :title"
        )->setParameters([
            "accountId" => $accountId,
            "date" => $date,
            "title" => $title
        ]); $resultSet = $query->getResult();

        if(count($resultSet) == 1)
            return $resultSet[0];
        else throw new RepositoryException("Surveillance introuvable !");

    }

}

?>