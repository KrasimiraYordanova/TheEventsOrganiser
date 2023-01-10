<?php

namespace App\Entity;

use App\Repository\GuestRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimeStampTrait;

#[ORM\Entity(repositoryClass: GuestRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Guest
{
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rdsvp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $diet = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;


    #[ORM\ManyToOne(inversedBy: 'guests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventList $eventList = null;

    #[ORM\ManyToOne(inversedBy: 'guests')]
    private ?Tabletab $tableTab = null;

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

    public function getRdsvp(): ?string
    {
        return $this->rdsvp;
    }

    public function setRdsvp(?string $rdsvp): self
    {
        $this->rdsvp = $rdsvp;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDiet(): ?string
    {
        return $this->diet;
    }

    public function setDiet(?string $diet): self
    {
        $this->diet = $diet;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

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

    public function getTableTab(): ?Tabletab
    {
        return $this->tableTab;
    }

    public function setTableTab(?Tabletab $tableTab): self
    {
        $this->tableTab = $tableTab;

        return $this;
    }
}
