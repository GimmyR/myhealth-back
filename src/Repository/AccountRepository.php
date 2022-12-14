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
            throw new RepositoryException("Veuillez bien remplir le formulaire !");

    }

    public function editAccount(Account $account, $reqData) {

        $acc = $this->checkAccount($account->getEmail(), $reqData->password);

        if(isset($reqData->firstname))
            $acc->setFirstname($reqData->firstname);
        else if(isset($reqData->lastname))
            $acc->setLastname($reqData->lastname);
        else if(isset($reqData->email))
            $acc->setEmail($reqData->email);
        else if(isset($reqData->newPassword))
            $acc->setPassword($reqData->newPassword);
        else throw new RepositoryException("Veuillez bien remplir le formulaire !");
        
        $this->getEntityManager()->flush();
        
        return $acc;

    }

    public function editAccountByCode(string $email, $sentCode, $trueCode, $newPassword) {

        if($sentCode != $trueCode)
            throw new RepositoryException("Le code est invalide !");
        else {
            $account = $this->findByEmail($email);
            $account->setPassword($newPassword);
            $this->getEntityManager()->flush();
            return $account;
        }

    }

    public function findByEmail(string $email) {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT a
            FROM App\Entity\Account a
            WHERE a.email = :email"
        )->setParameter("email", $email);

        $result = $query->getResult();

        if(count($result) == 1)
            return $result[0];
        else throw new RepositoryException("Compte associé à cette adresse email introuvable !");

    }

    public function createAccount(Account $account) {

        $entityManager = $this->getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        return $this->findById($account->getId());

    }

    public function confirmAccount(Account $account) {

        $acc = $this->findById($account->getId());
        $acc->setStatus(1);
        $this->getEntityManager()->flush();
        
        return $acc;

    }

    public function findById(int $id) {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT a
            FROM App\Entity\Account a
            WHERE a.id = :id"
        )->setParameter("id", $id);

        $result = $query->getResult();

        if(count($result) == 1)
            return $result[0];
        else throw new RepositoryException("Compte associé à cet identifiant introuvable !");

    }

}

?>