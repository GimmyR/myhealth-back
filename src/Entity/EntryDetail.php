<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntryDetailRepository::class)]
class EntryDetail {

    // ATTRIBUTES :

    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column()]
    protected int $id;

    #[ORM\Column()]
    protected int $entryId;

    #[ORM\Column()]
    protected int $parameterId;

    #[ORM\Column()]
    protected float $value;

    #[ORM\Column()]
    protected int $status;

    // CONSTRUCT :

    public function __construct(int $entryId, int $parameterId, float $value, int $status) {
        $this->setEntryId($entryId);
        $this->setParameterId($parameterId);
        $this->setValue($value);
        $this->setStatus($status);
    }

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
        $this->value = $value;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status) {
        $this->status = $status;
    }

    // METHODS :

    public function validateValue() {
        if($this->value < 0)
            throw new EntityException("Value is invalid !");
    }

    public function validateStatus() {
        if($this->status != 0 && $this->status != 1)
            throw new EntityException("Status is invalid !");
    }

    public function validate() {
        $this->validateValue();
        $this->validateStatus();
    }

}

?>