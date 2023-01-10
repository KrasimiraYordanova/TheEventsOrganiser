<?php

namespace App\Entity;

use App\Repository\EventListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimeStampTrait;

#[ORM\Entity(repositoryClass: EventListRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class EventList
{
    use TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $eventName = null;

    #[ORM\Column(length: 255)]
    private ?string $eventSlug = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $eventDate = null;

    #[ORM\Column]
    private ?float $eventBudget = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $eventLocation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'eventList')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventType $eventType = null;

    #[ORM\ManyToOne(inversedBy: 'eventLists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;
    
    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: EventProperty::class)]
    private Collection $eventProperties;

    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Rdv::class)]
    private Collection $rdvs;

    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Checklist::class)]
    private Collection $checklists;

    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Picture::class)]
    private Collection $pictures;
    
    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Guest::class)]
    private Collection $guests;

    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Expense::class)]
    private Collection $expenses;

    public function __construct()
    {
        
        $this->eventProperties = new ArrayCollection();
        $this->rdvs = new ArrayCollection();
        $this->checklists = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->guests = new ArrayCollection();
        $this->expenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): self
    {
        $this->eventName = $eventName;

        return $this;
    }

    public function getEventSlug(): ?string
    {
        return $this->eventSlug;
    }

    public function setEventSlug(string $eventSlug): self
    {
        $this->eventSlug = $eventSlug;

        return $this;
    }

    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->eventDate;
    }

    public function setEventDate(\DateTimeInterface $eventDate): self
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    public function getEventBudget(): ?float
    {
        return $this->eventBudget;
    }

    public function setEventBudget(float $eventBudget): self
    {
        $this->eventBudget = $eventBudget;

        return $this;
    }

    public function getEventLocation(): ?string
    {
        return $this->eventLocation;
    }

    public function setEventLocation(?string $eventLocation): self
    {
        $this->eventLocation = $eventLocation;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

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
            $eventProperty->setEventList($this);
        }

        return $this;
    }

    public function removeEventProperty(EventProperty $eventProperty): self
    {
        if ($this->eventProperties->removeElement($eventProperty)) {
            // set the owning side to null (unless already changed)
            if ($eventProperty->getEventList() === $this) {
                $eventProperty->setEventList(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rdv>
     */
    public function getRdvs(): Collection
    {
        return $this->rdvs;
    }

    public function addRdv(Rdv $rdv): self
    {
        if (!$this->rdvs->contains($rdv)) {
            $this->rdvs->add($rdv);
            $rdv->setEventList($this);
        }

        return $this;
    }

    public function removeRdv(Rdv $rdv): self
    {
        if ($this->rdvs->removeElement($rdv)) {
            // set the owning side to null (unless already changed)
            if ($rdv->getEventList() === $this) {
                $rdv->setEventList(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Checklist>
     */
    public function getChecklists(): Collection
    {
        return $this->checklists;
    }

    public function addChecklist(Checklist $checklist): self
    {
        if (!$this->checklists->contains($checklist)) {
            $this->checklists->add($checklist);
            $checklist->setEventList($this);
        }

        return $this;
    }

    public function removeChecklist(Checklist $checklist): self
    {
        if ($this->checklists->removeElement($checklist)) {
            // set the owning side to null (unless already changed)
            if ($checklist->getEventList() === $this) {
                $checklist->setEventList(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setEventList($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getEventList() === $this) {
                $picture->setEventList(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Guest>
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function addGuest(Guest $guest): self
    {
        if (!$this->guests->contains($guest)) {
            $this->guests->add($guest);
            $guest->setEventList($this);
        }

        return $this;
    }

    public function removeGuest(Guest $guest): self
    {
        if ($this->guests->removeElement($guest)) {
            // set the owning side to null (unless already changed)
            if ($guest->getEventList() === $this) {
                $guest->setEventList(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): self
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setEventList($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): self
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getEventList() === $this) {
                $expense->setEventList(null);
            }
        }

        return $this;
    }
}
