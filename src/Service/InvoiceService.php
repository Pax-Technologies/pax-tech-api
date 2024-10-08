<?php

namespace App\Service;

use App\Entity\Invoice;
use App\Entity\InvoiceDetail;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;


class InvoiceService
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    private $twig;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer, Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function checkInvoicesAndSendEmails(): void
    {
        $invoiceDetailRepository = $this->entityManager->getRepository(InvoiceDetail::class);

        // Change this line to find all InvoiceDetails instead of Invoices
        $invoiceDetails = $invoiceDetailRepository->findAll();

        $invoicesToBeCreated = [];

        foreach ($invoiceDetails as $invoiceDetail) {
            $nextInvoiceDate = clone $invoiceDetail->getInvoice()->getDate();
            if ($invoiceDetail->getPeriodicity() && $nextInvoiceDate->modify('+' . $invoiceDetail->getPeriodicity()) <= new \DateTimeImmutable()) {
                // Group invoice details by invoice ID
                $invoicesToBeCreated[$invoiceDetail->getInvoice()->getId()][] = $invoiceDetail;
            }
        }

        foreach ($invoicesToBeCreated as $invoiceId => $invoiceDetails) {
            // Use any of the invoice details to clone the invoice, they all belong to the same one.
            $invoice = $invoiceDetails[0]->getInvoice();
            $newInvoice = $invoice->cloneWithoutInvoiceDetails();
            $newInvoice->setDate(new \DateTime());
            $invoiceDate = $newInvoice->getDate()->format('Ym');  // Y = year (4 digits), m = month (2 digits)

            $newInvoice->setInvoiceNumber($invoiceDate);
            $newInvoice->setStatus(2); // Invoice is by default not paid

            // Create new invoice details based on the old ones
            foreach ($invoiceDetails as $invoiceDetail) {
                $newInvoiceDetail = $invoiceDetail->cloneWithoutPeriodicity();
                $newInvoiceDetail->setDescription("Renouvellement - " . $invoiceDetail->getDescription());
                $newInvoice->addInvoiceDetail($newInvoiceDetail);
            }

            // Persist the new invoice
            $this->entityManager->persist($newInvoice);
            $this->entityManager->flush();

            // After creating the new invoice, you can send the email.
            $this->sendEmail($newInvoice);
        }
    }

    public function sendEmail(Invoice $invoice)
    {

        $pdfOutput = $this->generatePdf($invoice);

        $message = '<p>Bonjour,</p><p>Veuillez trouver ci-joint votre facture de renouvellement des services suivants:</p><ul>';

        foreach ($invoice->getInvoiceDetails() as $invoiceDetail) {
            $message .= '<li>' . $invoiceDetail->getDescription() . '</li>';
        }

        $message .= '</ul><p>Cordialement,</p>';

        $email = (new TemplatedEmail())
            ->from('invoices@pax-tech.com')
            ->to($invoice->getClient()->getEmail())
            ->addBcc('paquesb@gmail.com')
            ->subject('Invoice ')
            ->htmlTemplate('emails/invoice_email.html.twig')
            ->context([
                'invoice' => $invoice,
                'message' => $message
            ])
            ->attach($pdfOutput, 'Facture_' . $invoice->getInvoiceNumber() . '.pdf', 'application/pdf');
        $email->getHeaders()->addTextHeader('X-Transport', 'invoices');
        $this->mailer->send($email);

        $invoice->setEmailSent(true);
        $this->entityManager->persist($invoice);
        $this->entityManager->flush();
    }

    private function generatePdf(Invoice $invoice): string
    {
        // Configuration Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsHtml5ParserEnabled(true);

        // Initialisation de Dompdf
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->set_option('isRemoteEnabled', true);
        $html = $this->twig->render('invoice/pdf_for_php.html.twig', [
            'invoice' => $invoice
        ]);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Retourner le contenu du PDF
        return $dompdf->output();
    }
}