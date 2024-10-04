<?php

namespace App\EventListener;

use App\Entity\Invoice;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class InvoiceListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $invoice = $args->getObject();

        // verifie si l'objet est une instance de Invoice
        if (!$invoice instanceof Invoice) {
            return;
        }

        // génère le numéro de facture
        $invoiceNumber = $this->generateInvoiceNumber($args);

        // assigne le numéro de facture à l'entité
        $invoice->setInvoiceNumber($invoiceNumber);
    }

    private function generateInvoiceNumber(LifecycleEventArgs $args): string
    {
        $invoice = $args->getObject();
        $entityManager = $args->getObjectManager();

        // récupère l'année et le mois à partir du numéro de facture
        $yearMonth = substr($invoice->getInvoiceNumber(), 0, 6);

        // compte les factures pour ce mois et cette année
        $criteria = Criteria::create()
            ->where(Criteria::expr()->startsWith('invoiceNumber', $yearMonth))
            ->orderBy(['invoiceNumber' => Criteria::DESC])
            ->setMaxResults(1);

        $lastInvoice = $entityManager->getRepository(Invoice::class)->matching($criteria)->first();

        if ($lastInvoice) {
            $lastSequence = substr($lastInvoice->getInvoiceNumber(), 6);
        } else {
            $lastSequence = 0;
        }

// crée un numéro de facture en concaténant l'année, le mois et le dernier numéro de séquence + 1
        $invoiceNumber = $yearMonth . str_pad((string) ($lastSequence + 1), 2, "0", STR_PAD_LEFT);

        return $invoiceNumber;
    }
}