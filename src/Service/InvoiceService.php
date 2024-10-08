<?php

namespace App\Service;

use App\Entity\Invoice;
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
        $invoiceRepository = $this->entityManager->getRepository(Invoice::class);

        $invoices = $invoiceRepository->findAll();

        foreach ($invoices as $invoice) {
            $nextInvoiceDate = clone $invoice->getDate();
            if ($invoice->getPeriodicity() && $nextInvoiceDate->modify('+' . $invoice->getPeriodicity()) <= new \DateTimeImmutable()) {
                // Créer une nouvelle facture avec la même date de facturation
                $newInvoice = $invoice->cloneWithoutInvoiceDetails();
                $newInvoice->setDate(new \DateTime());
                $invoiceDate = $newInvoice->getDate()->format('Ym');  // Y = year (4 digits), m = month (2 digits)

                $newInvoice->setInvoiceNumber($invoiceDate);
                $newInvoice->setStatus(2); // La facture est par défaut non payée

                // Créer de nouvelles factures détails basées sur les anciennes
                foreach ($invoice->getInvoiceDetails() as $invoiceDetail) {
                    $newInvoiceDetail = clone $invoiceDetail;
                    $newInvoiceDetail->setDescription("Renouvellement - " . $invoiceDetail->getDescription());
                    $newInvoice->addInvoiceDetail($newInvoiceDetail);
                }
//                @TODO il y a les invoices details de la facture originelle sur les novelles factures

                // Persist the new invoice
                $this->entityManager->persist($newInvoice);
                $this->entityManager->flush();

                // Après avoir créé la nouvelle facture, vous pouvez envoyer l'email
                $this->sendEmail($newInvoice);
            }
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