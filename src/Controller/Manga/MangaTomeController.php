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
            'navLinkId' => 'manga-tome',
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
            $cheminDossierBase = "/public/image/manga/cover/";
            $nomManga = $mangaTome->getManga()->getSlug();
            $cheminDossierManga = $this->getParameter('kernel.project_dir') . $cheminDossierBase . $nomManga;

            // Création du dossier s'il n'existe pas
            if (!file_exists($cheminDossierManga)) {
                mkdir($cheminDossierManga, 0777, true);
            }

            // Nom de fichier pour l'image (peut être personnalisé si nécessaire)
            $nomFichierImage = $nomManga.$mangaTome->getTomeNumber().'.jpeg';

            // Chemin complet de destination pour enregistrer l'image
            $cheminImageDestination = $cheminDossierManga . "/" . $nomFichierImage;

            // Téléchargement et enregistrement de l'image
            if (imagejpeg($cover, $cheminImageDestination, 100)) {
                $mangaTome->setCover($cheminDossierBase . $nomManga . "/" . $nomFichierImage);
            } else {
                $mangaTome->setCover(null);
            }

            $managerRegistry->getManager()->persist($mangaTome);
            $managerRegistry->getManager()->flush();

            $this->addFlash('success', 'Tome ajouté');

            return $this->redirectToRoute('manga_tome');
        }
        return $this->render('manga/manga_tome/add.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'MangaController',
            'navLinkId' => 'manga-tome',
        ]);
    }

    #[Route('manga/tome/edit/{id}', name: 'manga_tome_edit')]
    public function edit(ManagerRegistry $managerRegistry, Request $request, MangaTomeRepository $mangaTomeRepository, $id): Response
    {

        $mangaTome = $mangaTomeRepository->findOneBy(['id' => $id]);

        if(!$mangaTome){

            $this->addFlash('error', 'Pas de manga');

            $this->redirectToRoute('manga');

        }

        $form = $this->createForm(MangaTomeType::class, $mangaTome);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            // Lien de l'image à télécharger
            $lienImage = $mangaTome->getCover();

            if (!str_starts_with($lienImage, "http://") || !str_starts_with($lienImage, "https://")) {

                $cover = imagecreatefromstring(file_get_contents($lienImage));

                // Chemin où enregistrer l'image
                $cheminDossierBase = "/public/image/manga/cover/";
                $nomManga = $mangaTome->getManga()->getSlug();
                $cheminDossierManga = $this->getParameter('kernel.project_dir') . $cheminDossierBase . $nomManga;

                // Création du dossier s'il n'existe pas
                if (!file_exists($cheminDossierManga)) {
                    mkdir($cheminDossierManga, 0777, true);
                }

                // Nom de fichier pour l'image (peut être personnalisé si nécessaire)
                $nomFichierImage = $nomManga.$mangaTome->getTomeNumber().'.jpeg';

                // Chemin complet de destination pour enregistrer l'image
                $cheminImageDestination = $cheminDossierManga . "/" . $nomFichierImage;

                // Téléchargement et enregistrement de l'image
                if (imagejpeg($cover, $cheminImageDestination, 100)) {
                    $mangaTome->setCover($cheminDossierBase . $nomManga . "/" . $nomFichierImage);
                } else {
                    $mangaTome->setCover(null);
                }

            }

            $managerRegistry->getManager()->persist($mangaTome);
            $managerRegistry->getManager()->flush();

            $this->addFlash('success', 'Tome modifié');

            return $this->redirectToRoute('manga_details', [
                'id' => $mangaTome->getManga()->getId()
            ]);
        }

        return $this->render('manga/manga_tome/add.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'MangaController',
            'navLinkId' => 'manga-tome',
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
