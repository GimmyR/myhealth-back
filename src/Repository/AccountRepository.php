<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AccountRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        
        parent::__construct($registry, Account::class);

    }

    public function checkAccount(string $email, string $password) {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Account a
            WHERE a.email = :email
            AND a.password = :password'
        )->setParameters([

            'email' => $email,
            'password' => $password

        ]); return $query->getResult();

    }

}

?>