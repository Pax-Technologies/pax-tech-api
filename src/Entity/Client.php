<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ApiResource]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $VATNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    /**
     * @var Collection<int, Adres>
     */
    #[ORM\ManyToMany(targetEntity: Adres::class, mappedBy: 'client')]
    private Collection $adres;

    public function __construct()
    {
        $this->adres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getVATNumber(): ?string
    {
        return $this->VATNumber;
    }

    public function setVATNumber(?string $VATNumber): static
    {
        $this->VATNumber = $VATNumber;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Adres>
     */
    public function getAdres(): Collection
    {
        return $this->adres;
    }

    public function addAdre(Adres $adre): static
    {
        if (!$this->adres->contains($adre)) {
            $this->adres->add($adre);
            $adre->addClient($this);
        }

        return $this;
    }

    public function removeAdre(Adres $adre): static
    {
        if ($this->adres->removeElement($adre)) {
            $adre->removeClient($this);
        }

        return $this;
    }
}
