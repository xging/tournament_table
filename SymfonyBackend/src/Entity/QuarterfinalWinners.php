<?php

namespace App\Entity;

use App\Repository\QuarterfinalWinnersRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Interfaces\TeamInterface;

#[ORM\Entity(repositoryClass: QuarterfinalWinnersRepository::class)]
class QuarterfinalWinners implements TeamInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $teamName = null;

    #[ORM\Column]
    private ?bool $picked_flag = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }

    public function setTeamName(string $teamName): static
    {
        $this->teamName = $teamName;

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
