<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'Parameter')]
class Parameter {

    // ATTRIBUTES :

    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column()]
    protected int $id;

    #[ORM\Column(name: 'oversightId')]
    protected int $oversightId;

    #[ORM\Column()]
    protected string $name;

    #[ORM\Column()]
    protected $unit;

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

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function getUnit(): string {
        return $this->unit;
    }

    public function setUnit($unit) {
        $this->unit = $unit;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status) {
        $this->status = $status;
    }

    // METHODS :

    public function validateName() {
        if(empty($this->name))
            throw new EntityException("Name is invalid !");
    }

    public function validateStatus() {
        if($this->status != 0 && $this->status != 1)
            throw new EntityException("Status is invalid !");
    }

    public function validate() {
        $this->validateName();
        $this->validateStatus();
    }

}

?>