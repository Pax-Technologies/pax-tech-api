<?php

namespace App\Controller\Admin;

use App\Entity\Blog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
//        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/dashboard.html.twig');
    }

    // Méthode pour l'envoi d'email
    #[Route('/send-email', name: 'send_email_route', methods: ['POST'])]
    public function sendEmail(Request $request, MailerInterface $mailer): Response
    {
        $emailAddress = $request->request->get('email');

        // Créer le contenu HTML
        $htmlContent = $this->renderView('emails/template.html.twig', [
            'name' => 'Utilisateur',
        ]);

        // Créer et envoyer l'email
        $email = (new Email())
            ->from('support@pax-tech.com')
            ->to($emailAddress)
            ->subject('Configuration des emails')
            ->html($htmlContent);

        $mailer->send($email);
        $this->addFlash('success', 'Email envoyé avec succès à ' . $emailAddress);

        return $this->redirectToRoute('admin'); // Rediriger vers le dashboard
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="public/Logos/Web/PNG/RGB Negatif/300/PaxTech-Horizontal-Negatif-RGB-300[Cropped].png" height="75">')
            ->setFaviconPath('favicon.ico')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Console', 'fa fa-home');
        yield MenuItem::linkToCrud('Blog', 'fa fa-book', Blog::class);
        yield MenuItem::linkToRoute('Factures', 'fa fa-cogs', 'invoices');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('styles/admin.css');
    }
}
