<?php

namespace App\Entity;

use App\Repository\WallpointRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WallpointRepository::class)]
class Wallpoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'wallpoints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plan $plan = null;

    #[ORM\Column]
    private ?int $positionX = null;

    #[ORM\Column]
    private ?int $positionY = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): static
    {
        $this->plan = $plan;

        return $this;
    }

    public function getPositionX(): ?int
    {
        return $this->positionX;
    }

    public function setPositionX(int $positionX): static
    {
        $this->positionX = $positionX;

        return $this;
    }

    public function getPositionY(): ?int
    {
        return $this->positionY;
    }

    public function setPositionY(int $positionY): static
    {
        $this->positionY = $positionY;

        return $this;
    }
}
