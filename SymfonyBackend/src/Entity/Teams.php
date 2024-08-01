<?php

namespace App\Entity;

use App\Repository\Teams\TeamsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamsRepository::class)]
class Teams
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: "Divisions", fetch: "EAGER")]
    #[ORM\JoinColumn(name: "division_id", referencedColumnName: "id", nullable: true)]
    private ?Divisions $division = null;

    #[ORM\Column(length: 255)]
    private ?string $shortname = null;

    #[ORM\Column(nullable: true)]
    private ?bool $pickedFlag = null;

    #[ORM\Column]
    private ?int $team_id = null;

    #[ORM\Column]
    private ?int $result = null;

    #[ORM\Column]
    private ?int $divisionId =null;
    
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

    public function getDivision(): ?Divisions
    {
        return $this->division;
    }

    public function setDivision(?Divisions $division): self
    {
        $this->division = $division;
        return $this;
    }

    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname): self
    {
        $this->shortname = $shortname;
        return $this;
    }

    public function isPickedFlag(): ?bool
    {
        return $this->pickedFlag;
    }

    public function setPickedFlag(?bool $pickedFlag): self
    {
        $this->pickedFlag = $pickedFlag;
        return $this;
    }

    public function getTeamId(): ?int
    {
        return $this->team_id;
    }

    public function setTeamId(int $team_id): static
    {
        $this->team_id = $team_id;

        return $this;
    }

    public function getResult(): ?int
    {
        return $this->result;
    }

    public function getDivisionId(): ?int
    {
        return $this->divisionId;
    }

    public function setDivisionId(int $divisionId): static
    {   $this->divisionId = $divisionId;
        return $this;
    }


    public function setResult(int $result): static
    {
        $this->result = $result;

        return $this;
    }
}
