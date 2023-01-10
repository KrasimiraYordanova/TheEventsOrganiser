<?php

namespace App\Entity;

use App\Repository\ChecklistRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimeStampTrait;

#[ORM\Entity(repositoryClass: ChecklistRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Checklist
{
    use TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isChecked = null;

    #[ORM\ManyToOne(inversedBy: 'checklists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventList $eventList = null;

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function isIsChecked(): ?bool
    {
        return $this->isChecked;
    }

    public function setIsChecked(?bool $isChecked): self
    {
        $this->isChecked = $isChecked;

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
