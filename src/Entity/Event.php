<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
// use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;
/**
 * @Hateoas\Relation(
 *  "self",
 *  href= @Hateoas\Route(
 *      "event.get", 
 *      parameters = {"idEvent" = "expr(object.getId())"}
 * ),
 * exclusion = @Hateoas\Exclusion(groups="getEvent")
 * )
 * @Hateoas\Relation(
 *  "up",
 *  href= @Hateoas\Route(
 *      "event.getAll", 
 * ),
 * exclusion = @Hateoas\Exclusion(groups="getAllEvent")
 * )
 */

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllEvent", "getEvent", "getArtiste"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAllEvent", "getEvent", "getArtiste"])]
    #[Assert\NotNull(message: 'Un event doit avoir un nom')]
    #[Assert\Length(min: 5, minMessage: 'Minimum 5 caractère')]
    #[OA\Property(type: 'string')]
    private ?string $eventName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["getAllEvent", "getEvent", "getArtiste"])]
    #[Assert\NotNull(message: 'Un event doit avoir une date')]
    private ?\DateTimeInterface $eventDate = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Groups(["getAllEvent", "getEvent"])]
    private ?Artiste $artist = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Groups(["getAllEvent", "getEvent"])]
    private ?Place $place = null;

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

    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->eventDate;
    }

    public function setEventDate(\DateTimeInterface $eventDate): self
    {
        $this->eventDate = $eventDate;

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

    public function getArtist(): ?Artiste
    {
        return $this->artist;
    }

    public function setArtist(?Artiste $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }
}
