<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\TimeStampTrait;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Picture
{
    use TimeStampTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $namePath = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $album = null;

    #[ORM\ManyToOne(inversedBy: 'picture')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventList $eventList = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNamePath(): ?string
    {
        return $this->namePath;
    }

    public function setNamePath(string $namePath): self
    {
        $this->namePath = $namePath;

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

    public function getAlbum(): ?string
    {
        return $this->album;
    }

    public function setAlbum(string $album): self
    {
        $this->album = $album;

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
