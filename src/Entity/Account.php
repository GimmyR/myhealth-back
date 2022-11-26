<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'Account')] // Il faut preciser 'Account' avec un majuscule ici
                             // car ca cree un conflit au sein de la base de donnees
class Account {

    // ATTRIBUTES :

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected int $id;

    #[ORM\Column]
    protected string $firstname;

    #[ORM\Column]
    protected string $lastname;

    #[ORM\Column]
    protected string $email;

    #[ORM\Column]
    protected string $password;

    #[ORM\Column]
    protected int $status;

    protected static $specialCharacters = [
        '/[.\'"^£$%&*()}{@#~?><>,|=_+¬-]/',
        '/[\'"^£$%&*()}{#~?><>,|=_+¬-]/'
    ];

    // GETTERS AND SETTERS :

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname) {
        if($firstname == null || strlen($firstname) > 100 || preg_match(Account::$specialCharacters[0], $firstname))
            throw new EntityException("Votre prénom est invalide !");
        else
            $this->firstname = $firstname;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function setLastname(string $lastname) {
        if($lastname == null || strlen($lastname) > 100 || preg_match(Account::$specialCharacters[0], $lastname))
            throw new EntityException("Votre nom est invalide !");
        else
            $this->lastname = $lastname;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email) {
        if($email == null || strlen($email) > 255 || preg_match(Account::$specialCharacters[1], $email))
            throw new EntityException("Votre adresse email est invalide !");
        else
            $this->email = $email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password) {
        if($password == null || strlen($password) > 50 || preg_match(Account::$specialCharacters[1], $password))
            throw new EntityException("Votre mot de passe est invalide !");
        else
            $this->password = $password;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status) {
        if($status != 0 && $status != 1)
            throw new EntityException("Le statut du compte est invalide !");
        else
            $this->status = $status;
    }

    // METHODS :

}

?>