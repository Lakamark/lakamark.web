<?php

namespace App\Domain\Application\Entity;

use App\Domain\Application\Repository\ContentRepository;
use App\Domain\Attachment\Entity\Attachment;
use App\Domain\Auth\Entity\User;
use App\Domain\Blog\Entity\Post;
use App\Domain\Project\Entity\Project;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: ContentRepository::class)]
#[ORM\MappedSuperclass]
#[InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'kind', type: Types::STRING)]
#[ORM\DiscriminatorMap(['post' => Post::class, 'project' => Project::class])]
abstract class Content
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $isOnline = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'contents')]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Attachment::class, cascade: ['persist'], inversedBy: 'contents')]
    #[ORM\joinColumn(name: 'attachment_id', referencedColumnName: 'id')]
    private ?Attachment $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isOnline(): bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): static
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getImage(): ?Attachment
    {
        return $this->image;
    }

    public function setImage(?Attachment $image): static
    {
        $this->image = $image;

        return $this;
    }
}
