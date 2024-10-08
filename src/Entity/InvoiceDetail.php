<?php

namespace App\Entity;

use App\Repository\InvoiceDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceDetailRepository::class)]
class InvoiceDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'invoiceDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Invoice $invoice = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $unitPriceExcl = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $totalExcl = null;

    #[ORM\Column]
    private ?float $vatRate = null;

    #[ORM\Column]
    private ?float $totalIncl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $periodicity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): static
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUnitPriceExcl(): ?float
    {
        return $this->unitPriceExcl;
    }

    public function setUnitPriceExcl(float $unitPriceExcl): static
    {
        $this->unitPriceExcl = $unitPriceExcl;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotalExcl(): ?float
    {
        return $this->totalExcl;
    }

    public function setTotalExcl(float $totalExcl): static
    {
        $this->totalExcl = $totalExcl;

        return $this;
    }

    public function getVatRate(): ?float
    {
        return $this->vatRate;
    }

    public function setVatRate(float $vatRate): static
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    public function getTotalIncl(): ?float
    {
        return $this->totalIncl;
    }

    public function setTotalIncl(float $totalIncl): static
    {
        $this->totalIncl = $totalIncl;

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

    public function cloneWithoutPeriodicity()
    {
        $newInvoiceDetail = new InvoiceDetail();
        $newInvoiceDetail->setInvoice($this->getInvoice());
        $newInvoiceDetail->setDescription($this->getDescription());
        $newInvoiceDetail->setUnitPriceExcl($this->getUnitPriceExcl());
        $newInvoiceDetail->setQuantity($this->getQuantity());
        $newInvoiceDetail->setTotalExcl($this->getTotalExcl());
        $newInvoiceDetail->setVatRate($this->getVatRate());
        $newInvoiceDetail->setTotalIncl($this->getTotalIncl());
        // Nous omettons setPeriodicity afin d'obtenir la nouvelle instance sans periodicity

        return $newInvoiceDetail;
    }
}
