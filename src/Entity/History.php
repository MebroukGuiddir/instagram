<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistoryRepository")
 */
class History
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\people", inversedBy="histories")
     */
    private $interactWith;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\account", inversedBy="histories")
     */
    private $fromAccount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getInteractWith(): ?people
    {
        return $this->interactWith;
    }

    public function setInteractWith(?people $interactWith): self
    {
        $this->interactWith = $interactWith;

        return $this;
    }

    public function getFromAccount(): ?account
    {
        return $this->fromAccount;
    }

    public function setFromAccount(?account $fromAccount): self
    {
        $this->fromAccount = $fromAccount;

        return $this;
    }
}
