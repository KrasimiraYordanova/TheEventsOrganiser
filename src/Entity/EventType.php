<?php

namespace App\Entity;

use App\Repository\EventTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimeStampTrait;

#[ORM\Entity(repositoryClass: EventTypeRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class EventType
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

    #[ORM\OneToMany(mappedBy: 'eventType', targetEntity: Property::class)]
    private Collection $property;

    #[ORM\OneToMany(mappedBy: 'eventType', targetEntity: EventList::class)]
    private Collection $eventList;

    public function __construct()
    {
        $this->property = new ArrayCollection();
        $this->eventList = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Property>
     */
    public function getProperty(): Collection
    {
        return $this->property;
    }

    public function addProperty(Property $property): self
    {
        if (!$this->property->contains($property)) {
            $this->property->add($property);
            $property->setEventType($this);
        }

        return $this;
    }

    public function removeProperty(Property $property): self
    {
        if ($this->property->removeElement($property)) {
            // set the owning side to null (unless already changed)
            if ($property->getEventType() === $this) {
                $property->setEventType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventList>
     */
    public function getEventList(): Collection
    {
        return $this->eventList;
    }

    public function addEventList(EventList $eventList): self
    {
        if (!$this->eventList->contains($eventList)) {
            $this->eventList->add($eventList);
            $eventList->setEventType($this);
        }

        return $this;
    }

    public function removeEventList(EventList $eventList): self
    {
        if ($this->eventList->removeElement($eventList)) {
            // set the owning side to null (unless already changed)
            if ($eventList->getEventType() === $this) {
                $eventList->setEventType(null);
            }
        }

        return $this;
    }
}
