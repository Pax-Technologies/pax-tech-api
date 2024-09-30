<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\InvoiceType;
use App\Entity\Invoice;

class InvoiceController extends AbstractController
{
    #[Route('/invoices', name: 'app_invoice')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
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

        return $this->render('invoice/invoice.html.twig', [
            'invoiceForm' => $form->createView(),
        ]);
    }
}