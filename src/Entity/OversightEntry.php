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
        if($status != 0 && $status != 1)
            throw new EntityException("Le statut de l'entrée est invalide !");
        else
            $this->status = $status;
    }

    // METHODS :

}

?>