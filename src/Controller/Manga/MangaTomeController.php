<?php

namespace App\Controller\Manga;

use App\Entity\MangaTome;
use App\Form\MangaTomeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MangaTomeController extends AbstractController
{
    #[Route('/manga/tome', name: 'manga_tome')]
    public function index(): Response
    {
        return $this->render('manga/manga_tome/index.html.twig', [
            'controller_name' => 'MangaTomeController',
        ]);
    }
    
    #[Route('manga/tome/add', name: 'manga_tome_add')]
    public function add(ManagerRegistry $managerRegistry, Request $request): Response
    {

        $mangaTome = new MangaTome();

        $form = $this->createForm(MangaTomeType::class, $mangaTome);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            // Lien de l'image à télécharger
            $lienImage = $mangaTome->getCover();
                
            $cover = imagecreatefromstring(file_get_contents($lienImage));

            // Chemin où enregistrer l'image
            $cheminDossierBase = "../public/image/manga/cover/";
            $nomManga = $mangaTome->getManga()->getSlug();
            $cheminDossierManga = $cheminDossierBase . $nomManga;

            // Création du dossier s'il n'existe pas
            if (!file_exists($cheminDossierManga)) {
                mkdir($cheminDossierManga, 0777, true);
            }

            // Nom de fichier pour l'image (peut être personnalisé si nécessaire)
            $nomFichierImage = $nomManga.$mangaTome->getTomeNumber();

            // Chemin complet de destination pour enregistrer l'image
            $cheminImageDestination = $cheminDossierManga . "/" . $nomFichierImage.'.jpeg';

            // Téléchargement et enregistrement de l'image
            if (imagejpeg($cover, $cheminImageDestination, 100)) {
                $mangaTome->setCover($cheminImageDestination);
            } else {
                $mangaTome->setCover(null);
            }

            $managerRegistry->getManager()->persist($mangaTome);
            $managerRegistry->getManager()->flush();
            
            $this->addFlash('success', 'Tome ajouté');

            return $this->redirectToRoute('manga_tome_add');
        }
        return $this->render('manga/manga_tome/add.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'MangaController',
        ]);
    }
}
