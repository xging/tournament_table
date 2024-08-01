<?php

namespace App\Entity;

use App\Repository\SemifinalWinnersRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Interfaces\TeamInterface;
#[ORM\Entity(repositoryClass: SemifinalWinnersRepository::class)]
class SemifinalWinners implements TeamInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $team_name = null;

    #[ORM\Column]
    private ?bool $picked_flag = null;

    public function getId(): ?int
    {
        return $this->id;
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
