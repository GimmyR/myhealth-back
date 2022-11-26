<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'EntryDetail')]
class EntryDetail {

    // ATTRIBUTES :

    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column()]
    protected int $id;

    #[ORM\Column(name: 'entryId')]
    protected int $entryId;

    #[ORM\Column(name: 'parameterId')]
    protected int $parameterId;

    #[ORM\Column()]
    protected float $value;

    #[ORM\Column()]
    protected int $status;

    // GETTERS AND SETTERS :

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getEntryId(): int {
        return $this->entryId;
    }

    public function setEntryId(int $entryId) {
        $this->entryId = $entryId;
    }

    public function getParameterId(): int {
        return $this->parameterId;
    }

    public function setParameterId(int $parameterId) {
        $this->parameterId = $parameterId;
    }

    public function getValue(): float {
        return $this->value;
    }

    public function setValue(float $value) {
        if($value < 0)
            throw new EntityException("La valeur d'une donnée est invalide !");
        else
            $this->value = $value;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status) {
        if($status != 0 && $status != 1)
            throw new EntityException("Le statut d'une donnée est invalide !");
        else
            $this->status = $status;
    }

    // METHODS :

}

?>