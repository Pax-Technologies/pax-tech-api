<?php

namespace App\EventListener;

use App\Entity\Invoice;
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

        // si la facture a déjà un numéro, ne rien faire
        if ($invoice->getInvoiceNumber() !== null) {
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

        // récupère l'année et le mois actuel
        $yearMonth = date('Ym');

        // compte les factures pour ce mois et cette année
        $count = $entityManager->getRepository(Invoice::class)->count(['invoiceNumber' => $yearMonth]);

        // crée un numéro de facture en concaténant l'année et le mois avec le compteur + 1
        $invoiceNumber = $yearMonth . str_pad((string) ($count + 1), 3, "0", STR_PAD_LEFT);

        return $invoiceNumber;
    }
}