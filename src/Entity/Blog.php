<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Repository\BlogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(paginationItemsPerPage: 3)]
#[ORM\Entity(repositoryClass: BlogRepository::class)]
#[Vich\Uploadable]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
#[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titleFR = null;

    #[ORM\Column(length: 255)]
    private ?string $titleEN = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creation_date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitleFR = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitleEN = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domainFR = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domainEN = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentFR = null; 

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentEN = null;

    #[Vich\UploadableField(mapping: 'images', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(length: 255, unique: true)]
    #[ApiProperty(identifier: true)]
    private ?string $slug = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitleFR(): ?string
    {
        return $this->titleFR;
    }

    public function setTitleFR(string $titleFR): void
    {
        $this->titleFR = $titleFR;
    }

    public function getTitleEN(): ?string
    {
        return $this->titleEN;
    }

    public function setTitleEN(string $titleEN): void
    {
        $this->titleEN = $titleEN;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): void
    {
        $this->creation_date = $creation_date;
    }

    public function getSubtitleFR(): ?string
    {
        return $this->subtitleFR;
    }

    public function setSubtitleFR(?string $subtitleFR): void
    {
        $this->subtitleFR = $subtitleFR;
    }

    public function getSubtitleEN(): ?string
    {
        return $this->subtitleEN;
    }

    public function setSubtitleEN(?string $subtitleEN): void
    {
        $this->subtitleEN = $subtitleEN;
    }

    public function getDomainFR(): ?string
    {
        return $this->domainFR;
    }

    public function setDomainFR(?string $domainFR): void
    {
        $this->domainFR = $domainFR;
    }

    public function getDomainEN(): ?string
    {
        return $this->domainEN;
    }

    public function setDomainEN(?string $domainEN): void
    {
        $this->domainEN = $domainEN;
    }

    public function getContentFR(): ?string  
    {
        return $this->contentFR;
    }

    public function setContentFR(string $contentFR): void 
    {
        $this->contentFR = $contentFR;
    }

    public function getContentEN(): ?string  
    {
        return $this->contentEN;
    }

    public function setContentEN(string $contentEN): void 
    {
        $this->contentEN = $contentEN;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}