<?php

namespace App\Controller\Manga;

use App\Entity\MangaTome;
use App\Form\MangaTomeType;
use App\Repository\MangaTomeRepository;
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

    #[Route('manga/tome/start/{id}', name: 'manga_tome_start')]
    public function start(ManagerRegistry $managerRegistry, MangaTomeRepository $mangaTomeRepository, $id): Response
    {
        $tome = $mangaTomeRepository->findOneBy(['id' => $id]);

        if ($tome->getReadingStartDate()){

            $this->addFlash('error', 'Manga déjà commencé');

            return $this->redirectToRoute('manga_details', [
                'id' => $tome->getManga()->getId(),
            ]);
        }

        $tome->setReadingStartDate(new \DateTime());

        $managerRegistry->getManager()->persist($tome);
        $managerRegistry->getManager()->flush();

        $this->addFlash('success', 'Manga commencé');

        return $this->redirectToRoute('manga_details', [
            'id' => $tome->getManga()->getId(),
        ]);
    }

    #[Route('manga/tome/read/{id}', name: 'manga_tome_read')]
    public function read(ManagerRegistry $managerRegistry, MangaTomeRepository $mangaTomeRepository, $id): Response
    {
        $tome = $mangaTomeRepository->findOneBy(['id' => $id]);

        if ($tome->getReadingEndDate()){

            $this->addFlash('error', 'Manga déjà terminé');

            return $this->redirectToRoute('manga_details', [
                'id' => $tome->getManga()->getId(),
            ]);
        }

        $tome->setReadingEndDate(new \DateTime());

        $managerRegistry->getManager()->persist($tome);
        $managerRegistry->getManager()->flush();

        $this->addFlash('success', 'Manga terminé');

        return $this->redirectToRoute('manga_details', [
            'id' => $tome->getManga()->getId(),
        ]);
    }
}
