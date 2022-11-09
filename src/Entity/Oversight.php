<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Oversight {

    // ATTRIBUTES :

    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column()]
    protected int $id;

    #[ORM\Column(name: 'accountId')]
    protected int $accountId;

    #[ORM\Column()]
    protected string $date;

    #[ORM\Column()]
    protected string $title;

    #[ORM\Column()]
    protected int $status;

    // CONSTRUCT :

    public function __construct(int $accountId, string $date, string $title, int $status) {
        
        $this->setAccountId($accountId);
        $this->setDate($date);
        $this->setTitle($title);
        $this->setStatus($status);

    }

    // GETTERS AND SETTERS :

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getAccountId(): int {
        return $this->accountId;
    }

    public function setAccountId(int $accountId) {
        $this->accountId = $accountId;
    }

    public function getDate(): string {
        return $this->date;
    }

    public function setDate(string $date) {
        $this->date = $date;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title) {
        $this->title = $title;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status) {
        $this->status = $status;
    }

    // METHODS :

    public function validateTitle() {
        if(empty($this->title))
            throw new EntityException("Title is invalid !");
    }

    public function validateStatus() {
        if($this->status != 0 && $this->status != 1)
            throw new EntityException("Status is invalid !");
    }

    public function validate() {

        $this->validateTitle();
        $this->validateStatus();

    }

}

?>