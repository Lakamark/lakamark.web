<?php

namespace App\Domain\Project\Entity;

use App\Domain\Application\Entity\Content;
use App\Domain\Project\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project extends Content
{
    #[ORM\Column(length: 255, nullable: true)]
    private string $clientName = '';

    #[ORM\Column(length: 255, nullable: true)]
    private string $website = '';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $isPersonal = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $productAt = null;

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(?string $clientName): static
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function isPersonal(): bool
    {
        return $this->isPersonal;
    }

    public function setIsPersonal(bool $isPersonal): static
    {
        $this->isPersonal = $isPersonal;

        return $this;
    }

    public function getProductAt(): ?\DateTimeImmutable
    {
        return $this->productAt;
    }

    public function setProductAt(\DateTimeImmutable $productAt): static
    {
        $this->productAt = $productAt;

        return $this;
    }
}
