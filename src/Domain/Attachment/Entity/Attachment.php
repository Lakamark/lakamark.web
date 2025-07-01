<?php

namespace App\Domain\Attachment\Entity;

use App\Domain\Attachment\Repository\AttachmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
#[Vich\Uploadable]
class Attachment implements Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'attachments', fileNameProperty: 'filename', size: 'fileSize')]
    private ?File $file = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $fileName = '';

    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private int $fileSize = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): int
    {
        return $this->id ?: 0;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): static
    {
        $this->fileSize = $fileSize;

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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): Attachment
    {
        $this->file = $file;

        return $this;
    }

    public function __toString()
    {
        return $this->fileName;
    }
}
