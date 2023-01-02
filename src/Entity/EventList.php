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

    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Rdv::class)]
    private Collection $rdv;

    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Checklist::class)]
    private Collection $checklist;

    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Picture::class)]
    private Collection $picture;

    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Tabletab::class)]
    private Collection $tabletab;

    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Expense::class)]
    private Collection $expense;

    #[ORM\OneToMany(mappedBy: 'eventList', targetEntity: Guest::class)]
    private Collection $guest;

    public function __construct()
    {
        $this->rdv = new ArrayCollection();
        $this->checklist = new ArrayCollection();
        $this->picture = new ArrayCollection();
        $this->tabletab = new ArrayCollection();
        $this->expense = new ArrayCollection();
        $this->guest = new ArrayCollection();
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
     * @return Collection<int, Rdv>
     */
    public function getRdv(): Collection
    {
        return $this->rdv;
    }

    public function addRdv(Rdv $rdv): self
    {
        if (!$this->rdv->contains($rdv)) {
            $this->rdv->add($rdv);
            $rdv->setEventList($this);
        }

        return $this;
    }

    public function removeRdv(Rdv $rdv): self
    {
        if ($this->rdv->removeElement($rdv)) {
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
    public function getChecklist(): Collection
    {
        return $this->checklist;
    }

    public function addChecklist(Checklist $checklist): self
    {
        if (!$this->checklist->contains($checklist)) {
            $this->checklist->add($checklist);
            $checklist->setEventList($this);
        }

        return $this;
    }

    public function removeChecklist(Checklist $checklist): self
    {
        if ($this->checklist->removeElement($checklist)) {
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
    public function getPicture(): Collection
    {
        return $this->picture;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->picture->contains($picture)) {
            $this->picture->add($picture);
            $picture->setEventList($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->picture->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getEventList() === $this) {
                $picture->setEventList(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tabletab>
     */
    public function getTabletab(): Collection
    {
        return $this->tabletab;
    }

    public function addTabletab(Tabletab $tabletab): self
    {
        if (!$this->tabletab->contains($tabletab)) {
            $this->tabletab->add($tabletab);
            $tabletab->setEventList($this);
        }

        return $this;
    }

    public function removeTabletab(Tabletab $tabletab): self
    {
        if ($this->tabletab->removeElement($tabletab)) {
            // set the owning side to null (unless already changed)
            if ($tabletab->getEventList() === $this) {
                $tabletab->setEventList(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpense(): Collection
    {
        return $this->expense;
    }

    public function addExpense(Expense $expense): self
    {
        if (!$this->expense->contains($expense)) {
            $this->expense->add($expense);
            $expense->setEventList($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): self
    {
        if ($this->expense->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getEventList() === $this) {
                $expense->setEventList(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Guest>
     */
    public function getGuest(): Collection
    {
        return $this->guest;
    }

    public function addGuest(Guest $guest): self
    {
        if (!$this->guest->contains($guest)) {
            $this->guest->add($guest);
            $guest->setEventList($this);
        }

        return $this;
    }

    public function removeGuest(Guest $guest): self
    {
        if ($this->guest->removeElement($guest)) {
            // set the owning side to null (unless already changed)
            if ($guest->getEventList() === $this) {
                $guest->setEventList(null);
            }
        }

        return $this;
    }
}
