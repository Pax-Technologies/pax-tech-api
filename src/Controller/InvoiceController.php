<?php
namespace App\Controller;

use App\Form\EmailInvoiceType;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Invoice;

class InvoiceController extends AbstractController
{
    #[Route('/invoices', name: 'invoices')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $invoices = $entityManager->getRepository(Invoice::class)->findAll();
        $emailForm = $this->createForm(EmailInvoiceType::class);

        return $this->render('invoice/list.html.twig', [
            'invoices' => $invoices,
            'emailForm' => $emailForm->createView(),
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

    #[Route('/invoice/{id<\d+>}', name: 'invoice_show')]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/invoice/{id}/pdf', name: 'invoice_pdf')]
    public function viewPdf(Invoice $invoice): Response
    {
        $pdfOutput = $this->generatePdf($invoice);
        $companyNameWithUnderscores = str_replace(' ', '_', $invoice->getClient()->getCompany());

        return new Response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Facture_' . $invoice->getInvoiceNumber() . '_' . $companyNameWithUnderscores . '.pdf"'
        ]);
    }

    // Route pour envoyer le PDF par mail
    #[Route('/invoice/send', name: 'invoice_send', methods: ['POST'])]
    public function sendInvoiceByEmail(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): JsonResponse
    {
        $form = $this->createForm(EmailInvoiceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $invoiceId = $request->request->get('id'); // Récupère l'ID de la facture
            $invoice = $entityManager->getRepository(Invoice::class)->find($invoiceId);

            if (!$invoice) {
                return new JsonResponse(['success' => false, 'message' => 'Facture non trouvée.'], 404);
            }

            // Générer le PDF et préparer l'e-mail
            $pdfOutput = $this->generatePdf($invoice);
            $email = (new Email())
                ->from('invoices@pax-tech.com')
                ->to($data['to'])
                ->subject($data['subject'])
                ->text($data['message'])
                ->attach($pdfOutput, 'Facture_' . $invoice->getInvoiceNumber() . '.pdf', 'application/pdf');

            // Utiliser le transport "invoices"
            $email->getHeaders()->addTextHeader('X-Transport', 'invoices');
            $mailer->send($email);

            $invoice->setEmailSent(true);
            $entityManager->persist($invoice);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        // Si le formulaire n'est pas valide, renvoyer les erreurs
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $field = $error->getOrigin()->getName();  // Nom du champ avec erreur
            $errors[$field] = $error->getMessage();   // Message d'erreur
        }

        return new JsonResponse(['success' => false, 'errors' => $errors], 400);
    }


    #[Route('/invoice/{id}/html', name: 'invoice_html')]
    public function generatehtml(Invoice $invoice): Response
    {
        return $this->render('invoice/pdf_for_php.html.twig', [
            'invoice' => $invoice,
        ]);
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

        // Récupérer le contenu HTML
        $html = $this->renderView('invoice/pdf_for_php.html.twig', [
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
