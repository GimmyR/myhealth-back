<?php

namespace App\Entity;

use App\Repository\OversightRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'Oversight')]
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
        if(empty($title))
            throw new EntityException("Le titre est invalide !");
        else
            $this->title = $title;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status) {
        if($status != 0 && $status != 1)
            throw new EntityException("Le statut de la surveillance est invalide !");
        else
            $this->status = $status;
    }

    // METHODS :

}

?>