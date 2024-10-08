<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ApiResource]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'totalAmountNet')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column]
    private ?float $totalAmountNet = null;

    #[ORM\Column]
    private ?float $totalVatAmount = null;

    #[ORM\Column]
    private ?float $totalAmountGross = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $invoiceNumber = null;

    #[ORM\Column]
    private ?int $status = null;

    /**
     * @var Collection<int, InvoiceDetail>
     */
    #[ORM\OneToMany(targetEntity: InvoiceDetail::class, mappedBy: 'invoice', orphanRemoval: true, cascade: ["persist"])]
    private Collection $invoiceDetails;

    #[ORM\Column]
    private ?bool $emailSent = false;

    #[ORM\Column(nullable: true)]
    private ?string $periodicity = null;

    public function __construct()
    {
        $this->invoiceDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getTotalAmountNet(): ?float
    {
        return $this->totalAmountNet;
    }

    public function setTotalAmountNet(float $totalAmountNet): static
    {
        $this->totalAmountNet = $totalAmountNet;

        return $this;
    }

    public function getTotalVatAmount(): ?float
    {
        return $this->totalVatAmount;
    }

    public function setTotalVatAmount(float $totalVatAmount): static
    {
        $this->totalVatAmount = $totalVatAmount;

        return $this;
    }

    public function getTotalAmountGross(): ?float
    {
        return $this->totalAmountGross;
    }

    public function setTotalAmountGross(float $totalAmountGross): static
    {
        $this->totalAmountGross = $totalAmountGross;

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): static
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, InvoiceDetail>
     */
    public function getInvoiceDetails(): Collection
    {
        return $this->invoiceDetails;
    }

    public function addInvoiceDetail(InvoiceDetail $invoiceDetail): static
    {
        if (!$this->invoiceDetails->contains($invoiceDetail)) {
            $this->invoiceDetails->add($invoiceDetail);
            $invoiceDetail->setInvoice($this);
        }

        return $this;
    }

    public function removeInvoiceDetail(InvoiceDetail $invoiceDetail): static
    {
        if ($this->invoiceDetails->removeElement($invoiceDetail)) {
            // set the owning side to null (unless already changed)
            if ($invoiceDetail->getInvoice() === $this) {
                $invoiceDetail->setInvoice(null);
            }
        }

        return $this;
    }

    public function getStatusText()
    {
        switch($this->status) {
            case 1:
                return 'Payé';
            case 2:
                return 'Non payé';
            case 3:
                return 'Annulé';
            default:
                return 'Statut inconnu';
        }
    }

    public function isEmailSent(): ?bool
    {
        return $this->emailSent;
    }

    public function setEmailSent(bool $emailSent): static
    {
        $this->emailSent = $emailSent;

        return $this;
    }

    public function getPeriodicity(): ?string
    {
        return $this->periodicity;
    }

    public function setPeriodicity(?string $periodicity): static
    {
        $this->periodicity = $periodicity;

        return $this;
    }

    public function cloneWithoutInvoiceDetails()
    {
        $newInvoice = new Invoice();
        $newInvoice->setDate(clone $this->getDate());
        $newInvoice->setClient($this->getClient());
        $newInvoice->setTotalAmountNet($this->getTotalAmountNet());
        $newInvoice->setTotalVatAmount($this->getTotalVatAmount());
        $newInvoice->setTotalAmountGross($this->getTotalAmountGross());
        $newInvoice->setInvoiceNumber($this->getInvoiceNumber());
        $newInvoice->setStatus($this->getStatus());
        $newInvoice->setEmailSent($this->isEmailSent());
        $newInvoice->setPeriodicity($this->getPeriodicity());

        // pas besoin de définir InvoiceDetails car il est initialisé en tant que ArrayCollection vide dans le constructeur

        return $newInvoice;
    }
}
