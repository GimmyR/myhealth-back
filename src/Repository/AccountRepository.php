<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AccountRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        
        parent::__construct($registry, Account::class);

    }

    public function checkAccount($email, $password) {

        if($email != null && $password != null) {
        
            $entityManager = $this->getEntityManager();
            $query = $entityManager->createQuery(
                'SELECT a
                FROM App\Entity\Account a
                WHERE a.email = :email
                AND a.password = :password'
            )->setParameters([
                'email' => $email,
                'password' => $password
            ]); $result = $query->getResult();

            if(count($result) == 1)
                return $result[0];
            else
                throw new RepositoryException("Adresse email ou mot de passe erroné !");

        } else
            throw new RepositoryException("Une coordonnée est inexistante !");

    }

}

?>