<?php

namespace App\Entity;

use App\Repository\PlayoffMatchesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayoffMatchesRepository::class)]
class PlayoffMatches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $group_stage = null;

    #[ORM\Column(length: 255)]
    private ?string $teamName_1 = null;

    #[ORM\Column(length: 255)]
    private ?string $teamName_2 = null;

    #[ORM\Column]
    private ?int $teamScore_1 = null;

    #[ORM\Column]
    private ?int $teamScore_2 = null;

    #[ORM\Column(length: 255)]
    private ?string $group_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupStage(): ?string
    {
        return $this->group_stage;
    }

    public function setGroupStage(string $group_stage): static
    {
        $this->group_stage = $group_stage;

        return $this;
    }

    public function getTeamName1(): ?string
    {
        return $this->teamName_1;
    }

    public function setTeamName1(string $teamName_1): static
    {
        $this->teamName_1 = $teamName_1;

        return $this;
    }

    public function getTeamName2(): ?string
    {
        return $this->teamName_2;
    }

    public function setTeamName2(string $teamName_2): static
    {
        $this->teamName_2 = $teamName_2;

        return $this;
    }

    public function getTeamScore1(): ?int
    {
        return $this->teamScore_1;
    }

    public function setTeamScore1(int $teamScore_1): static
    {
        $this->teamScore_1 = $teamScore_1;

        return $this;
    }

    public function getTeamScore2(): ?int
    {
        return $this->teamScore_2;
    }

    public function setTeamScore2(int $teamScore_2): static
    {
        $this->teamScore_2 = $teamScore_2;

        return $this;
    }

    public function getGroupName(): ?string
    {
        return $this->group_name;
    }

    public function setGroupName(string $group_name): static
    {
        $this->group_name = $group_name;

        return $this;
    }
}
