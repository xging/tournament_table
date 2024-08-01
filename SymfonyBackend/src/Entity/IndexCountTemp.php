<?php

namespace App\Entity;

use App\Repository\IndexCountTempRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndexCountTempRepository::class)]
class IndexCountTemp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $indexId = null;

    #[ORM\Column(length: 255)]
    private ?string $stage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIndexId(): ?int
    {
        return $this->indexId;
    }

    public function setIndexId(int $indexId): static
    {
        $this->indexId = $indexId;

        return $this;
    }

    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(string $stage): static
    {
        $this->stage = $stage;

        return $this;
    }
}
