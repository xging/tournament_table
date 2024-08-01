<?php

namespace App\Entity;

use App\Repository\MatchesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchesRepository::class)]
class Matches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $division_id = null;

    #[ORM\Column]
    private ?int $team_1_id = null;

    #[ORM\Column]
    private ?int $team_2_id = null;

    #[ORM\Column(length: 255)]
    private ?string $result = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDivisionId(): ?int
    {
        return $this->division_id;
    }

    public function setDivisionId(int $division_id): static
    {
        $this->division_id = $division_id;

        return $this;
    }

    public function getTeam1Id(): ?int
    {
        return $this->team_1_id;
    }

    public function setTeam1Id(int $team_1_id): static
    {
        $this->team_1_id = $team_1_id;

        return $this;
    }

    public function getTeam2Id(): ?int
    {
        return $this->team_2_id;
    }

    public function setTeam2Id(int $team_2_id): static
    {
        $this->team_2_id = $team_2_id;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function setDivision(?Divisions $division): self
    {
        $this->division = $division;
        return $this;
    }

    public function setTeam1(?Teams $team1): self
    {
        $this->teams = $team1;
        return $this;
    }

    public function setTeam2(?Teams $team2): self
    {
        $this->teams = $team2;
        return $this;
    }


}

