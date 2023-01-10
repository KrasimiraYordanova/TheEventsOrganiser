<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimeStampTrait;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Property
{
    use TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $dataType = null;

    #[ORM\ManyToOne(inversedBy: 'property')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventType $eventType = null;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: EventProperty::class)]
    private Collection $eventProperties;

    public function __construct()
    {
        $this->eventProperties = new ArrayCollection();
    }

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

    public function getDataType(): ?string
    {
        return $this->dataType;
    }

    public function setDataType(string $dataType): self
    {
        $this->dataType = $dataType;

        return $this;
    }

    public function getEventType(): ?EventType
    {
        return $this->eventType;
    }

    public function setEventType(?EventType $eventType): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * @return Collection<int, EventProperty>
     */
    public function getEventProperties(): Collection
    {
        return $this->eventProperties;
    }

    public function addEventProperty(EventProperty $eventProperty): self
    {
        if (!$this->eventProperties->contains($eventProperty)) {
            $this->eventProperties->add($eventProperty);
            $eventProperty->setProperty($this);
        }

        return $this;
    }

    public function removeEventProperty(EventProperty $eventProperty): self
    {
        if ($this->eventProperties->removeElement($eventProperty)) {
            // set the owning side to null (unless already changed)
            if ($eventProperty->getProperty() === $this) {
                $eventProperty->setProperty(null);
            }
        }

        return $this;
    }
}
