<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @var Collection<int, Address>
     */
    #[ORM\ManyToMany(targetEntity: Address::class, mappedBy: 'client')]
    private Collection $address;

    public function __construct()
    {
        $this->address = new ArrayCollection();
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
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->address;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->address->contains($address)) {
            $this->address->add($address);
            $address->addClient($this);
        }

        return $this;
    }

    public function removeAdress(Address $address): static
    {
        if ($this->address->removeElement($address)) {
            $address->removeClient($this);
        }

        return $this;
    }

    public function getBillingAddress()
    {
        foreach ($this->address as $address) {
            if ($address->getType() === 1) {
                return $address;
            }
        }

        return null;
    }

    /**
     * @Assert\Callback
     */
    public function validateAddressTypes(ExecutionContextInterface $context, $payload)
    {
        // Collect all address types for this client
        $addressTypes = array_map(function (Address $address) {
            return $address->getType();
        }, $this->addresses->toArray());

        // Check for duplicates
        if (count($addressTypes) !== count(array_unique($addressTypes))) {
            $context->buildViolation('A client cannot have two addresses of the same type.')
                ->atPath('addresses')
                ->addViolation();
        }
    }
}
