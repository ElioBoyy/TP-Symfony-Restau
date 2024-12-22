<?php

namespace App\Entity;

use App\Repository\PlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Table>
     */
    #[ORM\OneToMany(targetEntity: Table::class, mappedBy: 'plan')]
    private Collection $tables;

    /**
     * @var Collection<int, Wallpoint>
     */
    #[ORM\OneToMany(targetEntity: Wallpoint::class, mappedBy: 'plan')]
    private Collection $wallpoints;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\ManyToOne(inversedBy: 'plans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    public function __construct()
    {
        $this->tables = new ArrayCollection();
        $this->wallpoints = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Table>
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(Table $table): static
    {
        if (!$this->tables->contains($table)) {
            $this->tables->add($table);
            $table->setPlan($this);
        }

        return $this;
    }

    public function removeTable(Table $table): static
    {
        if ($this->tables->removeElement($table)) {
            // set the owning side to null (unless already changed)
            if ($table->getPlan() === $this) {
                $table->setPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Wallpoint>
     */
    public function getWallpoints(): Collection
    {
        return $this->wallpoints;
    }

    public function addWallpoint(Wallpoint $wallpoint): static
    {
        if (!$this->wallpoints->contains($wallpoint)) {
            $this->wallpoints->add($wallpoint);
            $wallpoint->setPlan($this);
        }

        return $this;
    }

    public function removeWallpoint(Wallpoint $wallpoint): static
    {
        if ($this->wallpoints->removeElement($wallpoint)) {
            // set the owning side to null (unless already changed)
            if ($wallpoint->getPlan() === $this) {
                $wallpoint->setPlan(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): static
    {
        $this->restaurant = $restaurant;

        return $this;
    }
}
