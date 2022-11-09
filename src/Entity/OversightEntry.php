<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'OversightEntry')]
class OversightEntry {

    // ATTRIBUTES :

    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column()]
    protected int $id;

    #[ORM\Column(name: 'oversightId')]
    protected int $oversightId;

    #[ORM\Column()]
    protected string $date;

    #[ORM\Column()]
    protected int $status;

    // CONSTRUCT :

    public function __construct(int $oversightId, string $date, int $status) {
        $this->setOversightId($oversightId);
        $this->setDate($date);
        $this->setStatus($status);
    }

    // GETTERS AND SETTERS :

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getOversightId(): int {
        return $this->oversightId;
    }

    public function setOversightId(int $oversightId) {
        $this->oversightId = $oversightId;
    }

    public function getDate(): string {
        return $this->date;
    }

    public function setDate(string $date) {
        $this->date = $date;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status) {
        $this->status = $status;
    }

    // METHODS :

    public function validateStatus() {
        if($this->status != 0 && $this->status != 1)
            throw new EntityException("Status is invalid !");
    }

    public function validate() {
        $this->validateStatus();
    }

}

?>