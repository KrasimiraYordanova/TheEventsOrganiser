<?php

namespace App\Entity;

use App\Repository\ExpenseRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimeStampTrait;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Expense
{
    use TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?float $totalCost = null;

    #[ORM\Column(nullable: true)]
    private ?float $totalPaid = null;

    #[ORM\ManyToOne(inversedBy: 'expense')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventList $eventList = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTotalCost(): ?float
    {
        return $this->totalCost;
    }

    public function setTotalCost(?float $totalCost): self
    {
        $this->totalCost = $totalCost;

        return $this;
    }

    public function getTotalPaid(): ?float
    {
        return $this->totalPaid;
    }

    public function setTotalPaid(?float $totalPaid): self
    {
        $this->totalPaid = $totalPaid;

        return $this;
    }

    public function getEventList(): ?EventList
    {
        return $this->eventList;
    }

    public function setEventList(?EventList $eventList): self
    {
        $this->eventList = $eventList;

        return $this;
    }
}
