<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArtisteRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
// use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ArtisteRepository::class)]
class Artiste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllEvent", "getEvent", "getArtiste"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAllEvent", "getEvent", "getArtiste"])]
    private ?string $artistName = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAllEvent", "getEvent", "getArtiste"])]
    private ?string $artistCategory = null;

    #[ORM\OneToMany(mappedBy: 'artist', targetEntity: Event::class)]
    #[Groups(["getArtiste"])]
    private Collection $events;

    #[ORM\Column]
    private ?bool $status = null;


    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArtistName(): ?string
    {
        return $this->artistName;
    }

    public function setArtistName(string $artistName): self
    {
        $this->artistName = $artistName;

        return $this;
    }

    public function getArtistCategory(): ?string
    {
        return $this->artistCategory;
    }

    public function setArtistCategory(string $artistCategory): self
    {
        $this->artistCategory = $artistCategory;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setArtist($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getArtist() === $this) {
                $event->setArtist(null);
            }
        }

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
