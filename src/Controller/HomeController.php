<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\UploadType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $params, SessionInterface $session): Response
    {
        $invoices = $entityManager->getRepository(Invoice::class)->findUnsentInvoices();
        $uploadForm = $this->createForm(UploadType::class);
        $uploadForm->handleRequest($request);

        if ($uploadForm->isSubmitted() && $uploadForm->isValid()) {
            $uploadedFile = $uploadForm->get('file')->getData(); // Remplacez 'zipFile' par le nom de votre champ de fichier

            $zipFileRealPath = $uploadedFile->getRealPath();
            $zip = new \ZipArchive();

            $openStatus = $zip->open($zipFileRealPath);

            if($openStatus === true) {

                // Nous créons un dossier temporaire pour extraire les fichiers
                $tempExtractFolder = sys_get_temp_dir() . '/folder' . uniqid();

                if(!file_exists($tempExtractFolder)) {
                    mkdir($tempExtractFolder);
                }

                $zip->extractTo($tempExtractFolder);
                $zip->close();

                // Nous pouvons maintenant itérer sur chaque fichier du dossier temporaire pour les traiter un par un
                $dirFiles = new \DirectoryIterator($tempExtractFolder);
                $publicPath = $params->get('kernel.project_dir').'/public';

                foreach ($dirFiles as $file) {
                    if (!$file->isDot()) {
                        // Vérifiez que le fichier a l'extension ".cod"
                        $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
                        if ($extension !== "cod") {
                            $this->addFlash('dropzone_error', 'Le fichier "'
                                .$file->getFilename().'" a une extension incorrecte. Seuls les fichiers ".cod" sont autorisés.');
                            continue;
                        }

                        // Votre logique pour gérer chaque fichier ici ...
                        // Pour lire le contenu d'un fichier :
                        $fileContent = file_get_contents($file->getRealPath());

                        // Déplacer le fichier vers un nouveau répertoire
                        $destination = $publicPath.'/documents/codas/'.$file->getFilename();
                        rename($file->getRealPath(), $destination);
                    }
                }

// Afficher un message de réussite seulement si aucun message d'erreur n'a été ajouté
                if (!$session->getFlashBag()->has('dropzone_error')) {
                    $this->addFlash('dropzone_success', 'Les fichiers CODA ont bien été chargés');
                }

// Vous devriez effacer le dossier temporaire après avoir traité tous les fichiers
                $this->rmdirRecursive($tempExtractFolder);

                return $this->redirectToRoute('app_home');
            }
        }

//        return $this->redirectToRoute('admin');
        return $this->render('home/index.html.twig', [
            'invoices' => $invoices,
            'upload_form' => $uploadForm->createView(),
        ]);
    }

    private function rmdirRecursive($dir)
    {
        foreach(scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) continue;
            if (is_dir("$dir/$file")) $this->rmdirRecursive("$dir/$file");
            else unlink("$dir/$file");
        }
        rmdir($dir);
    }
}
