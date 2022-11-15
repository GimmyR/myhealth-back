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

    // CONSTRUCT :

    public function __construct(string $firstname, string $lastname, string $email, string $password, int $status) {

        $this->setFirstname($firstname);
        $this->setLastname($lastname);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setStatus($status);

    }

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
        $this->firstname = $firstname;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function setLastname(string $lastname) {
        $this->lastname = $lastname;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email) {
        $this->email = $email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password) {
        $this->password = $password;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status) {
        $this->status = $status;
    }

    // METHODS :

    public function validateFirstname() {
        if($this->firstname == null || strlen($this->firstname) > 100 || preg_match(Account::$specialCharacters[0], $this->firstname))
            throw new EntityException("First name is invalid !");
    }

    public function validateLastname() {
        if($this->lastname == null || strlen($this->lastname) > 100 || preg_match(Account::$specialCharacters[0], $this->lastname))
            throw new EntityException("Last name is invalid !");
    }

    public function validateEmail() {
        if($this->email == null || strlen($this->email) > 255 || preg_match(Account::$specialCharacters[1], $this->email))
            throw new EntityException("Email address is invalid !");
    }

    public function validatePassword() {
        if($this->password == null || strlen($this->password) > 50 || preg_match(Account::$specialCharacters[1], $this->password))
            throw new EntityException("Password is invalid !");
    }

    public function validateStatus() {
        if($this->status != 0 && $this->status != 1)
            throw new EntityException("Status is invalid !");
    }

    public function validate(): void {

        $this->validateFirstname();
        $this->validateLastname();
        $this->validateEmail();
        $this->validatePassword();
        $this->validateStatus();

    }

}

?>