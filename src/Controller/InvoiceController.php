<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\InvoiceType;
use App\Entity\Invoice;

class InvoiceController extends AbstractController
{
    #[Route('/invoices', name: 'invoices')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $invoices = $entityManager->getRepository(Invoice::class)->findAll();

        return $this->render('invoice/list.html.twig', [
            'invoices' => $invoices,
        ]);
    }
    #[Route('/invoice/create', name: 'create_invoice')]
    public function createInvoice(Request $request, EntityManagerInterface $entityManager): Response
    {
        $invoice = new Invoice(); // Créer une nouvelle instance de la facture

        $form = $this->createForm(InvoiceType::class, $invoice); // Créer le formulaire

        $form->handleRequest($request); // Traiter la demande HTTP incoming


        if ($form->isSubmitted() && $form->isValid()) {
            $billingMonth = $form->get('billingMonth')->getData();
            $billingYear = $form->get('billingYear')->getData();
            // Convertir le mois et l'année en un format utilisable pour générer le numéro de facture
            $invoiceDate = $billingYear . str_pad($billingMonth, 2, '0', STR_PAD_LEFT);

            $invoice->setInvoiceNumber($invoiceDate);  // Définir le numéro de facture
            // Ici, nous pourrions sauvegarder l'entité dans la base de données
             $entityManager->persist($invoice);
             $entityManager->flush();

            // Puis rediriger l'utilisateur vers une autre page
            return $this->redirectToRoute('invoices');
        }

        return $this->render('invoice/create.html.twig', [
            'invoiceForm' => $form->createView(),
        ]);
    }

    #[Route('/invoice/{id}', name: 'invoice_show')]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/invoice/{id}/pdf', name: 'invoice_pdf')]
    public function generatePdf(Invoice $invoice): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsHtml5ParserEnabled(true);
        $pdfOptions->setFontCache('fonts');

        // Instantiate Dompdf with options
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->set_option('isRemoteEnabled', true);

        // Récupérer le contenu HTML que tu veux convertir en PDF
        $html = $this->renderView('invoice/pdf_for_php.html.twig', [
            'invoice' => $invoice
        ]);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // (Facultatif) Configurer le format de la page et l'orientation
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le PDF
        $dompdf->render();

        // Stocker le PDF dans une variable pour envoi par email ou autre
        $output = $dompdf->output();

        $year = substr($invoice->getInvoiceNumber(), 0, 4);
        $month = substr($invoice->getInvoiceNumber(), 4, 2);

// Chemin où vous souhaitez sauvegarder le PDF
        $pdfDirectory = $this->getParameter('pdf_directory'); // Assurez-vous que ce paramètre est défini dans services.yaml

// Créer le chemin du dossier basé sur l'année et le mois
        $invoiceDirectory = $pdfDirectory . '/' . $year . '/' . $month;

// Créer le répertoire si il n'existe pas
        if (!file_exists($invoiceDirectory)) {
            mkdir($invoiceDirectory, 0777, true);
        }

        // Remplacez les espaces dans le nom de l'entreprise par des tirets bas
        $companyNameWithUnderscores = str_replace(' ', '_', $invoice->getClient()->getCompany());

        $pdfFilePath = $invoiceDirectory . '/Facture_'  . $invoice->getInvoiceNumber(). '_' . $companyNameWithUnderscores . '.pdf';

// Enregistrer le PDF sur le serveur
        file_put_contents($pdfFilePath, $output);

// Retourner la réponse avec le PDF généré
        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Facture_' . $invoice->getInvoiceNumber(). '_' . $companyNameWithUnderscores . '.pdf"',
        ]);
    }

    #[Route('/invoice/{id}/html', name: 'invoice_html')]
    public function generatehtml(Invoice $invoice): Response
    {
        // Configure Dompdf according to your needs
        // Retourner la réponse avec le PDF généré
        return $this->render('invoice/pdf_for_php.html.twig', [
            'invoice' => $invoice,
        ]);
    }

}