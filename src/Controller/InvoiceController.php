<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
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
    #[Route('/create_invoice', name: 'create_invoice')]
    public function createInvoice(Request $request, EntityManagerInterface $entityManager): Response
    {
        $invoice = new Invoice(); // Créer une nouvelle instance de la facture

        $form = $this->createForm(InvoiceType::class, $invoice); // Créer le formulaire

        $form->handleRequest($request); // Traiter la demande HTTP incoming

        if ($form->isSubmitted() && $form->isValid()) {
            // Ici, nous pourrions sauvegarder l'entité dans la base de données
             $entityManager->persist($invoice);
             $entityManager->flush();

            // Puis rediriger l'utilisateur vers une autre page
            // return $this->redirectToRoute('invoice_success');
        }

        return $this->render('invoice/create.html.twig', [
            'invoiceForm' => $form->createView(),
        ]);
    }

    #[Route('/invoice_show/{id}', name: 'invoice_show')]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('invoice/pdf/{id}', 'print_invoice')]
    public function generatePdf(
        $id,
        RequestStack $requestStack,
        EntityManagerInterface $entityManager
    ): Response {
        set_time_limit(0); // Désactive la limite de temps d'exécution

        $invoice = $entityManager->getRepository(Invoice::class)->find($id);

        if ($invoice === null) {
            throw $this->createNotFoundException('La facture demandée n\'existe pas.');
        }

        $baseUrl = $requestStack->getCurrentRequest()->getSchemeAndHttpHost();

        $process = new Process(['node', 'scripts/generate_pdf.js', $id, $baseUrl]);
        $process->setTimeout(120); // Définit le timeout à 120 secondes
        $process->run();

        if ($process->isSuccessful()) {
            return new Response('PDF généré avec succès.');
        }

        throw new \Exception($process->getErrorOutput());
    }
}