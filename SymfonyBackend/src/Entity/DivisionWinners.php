<?php

namespace App\Entity;
use App\Repository\DivisionWinners\DivisionWinnersRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Interfaces\TeamInterface;

#[ORM\Entity(repositoryClass: DivisionWinnersRepository::class)]
class DivisionWinners implements TeamInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $division_id = null;

    #[ORM\Column]
    private ?int $team_id = null;

    #[ORM\Column(length: 255)]
    private ?string $team_name = null;

    #[ORM\Column]
    private ?int $result = null;

    #[ORM\Column]
    private ?bool $picked_flag = null;

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

    public function getTeamId(): ?int
    {
        return $this->team_id;
    }

    public function setTeamId(int $team_id): static
    {
        $this->team_id = $team_id;

        return $this;
    }

    public function getTeamName(): ?string
    {
        return $this->team_name;
    }

    public function setTeamName(string $team_name): static
    {
        $this->team_name = $team_name;

        return $this;
    }

    public function getResult(): ?int
    {
        return $this->result;
    }

    public function setResult(int $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function isPickedFlag(): ?bool
    {
        return $this->picked_flag;
    }

    public function setPickedFlag(bool $picked_flag): static
    {
        $this->picked_flag = $picked_flag;

        return $this;
    }
}
