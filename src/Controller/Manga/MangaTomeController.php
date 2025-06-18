<?php

namespace App\Controller\Manga;

use App\Entity\MangaTome;
use App\Form\MangaTomeType;
use App\Repository\MangaRepository;
use App\Repository\MangaTomeRepository;
use App\Service\FileService;
use DateTime;
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
            'navLinkId' => 'manga_tome',
        ]);
    }

    #[Route('manga/tome/add/{id}', name: 'manga_tome_add')]
    public function add(ManagerRegistry $managerRegistry, Request $request, MangaRepository $mangaRepository, FileService $fileService, int $id = null): Response
    {

        $mangaTome = new MangaTome();

        if($id !== null) {
            $manga = $mangaRepository->find($id);
            $mangaTome->setManga($manga);
        }

        $form = $this->createForm(MangaTomeType::class, $mangaTome);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            if($mangaTome->getCover()) {
                // Lien de l'image à télécharger
                $link = $mangaTome->getCover();

                $cheminDossierManga = "/public/image/manga/cover/".$mangaTome->getManga()->getSlug();

                // Création du dossier s'il n'existe pas
                if (!file_exists($cheminDossierManga)) {
                    mkdir($cheminDossierManga, 0777, true);
                }

                // Nom de fichier pour l'image (peut être personnalisé si nécessaire)
                $nomFichierImage = $mangaTome->getManga()->getSlug().$mangaTome->getTomeNumber().'.jpeg';

                // Chemin complet de destination pour enregistrer l'image
                $destination = $cheminDossierManga."/".$nomFichierImage;

                // Téléchargement et enregistrement de l'image
                if ($fileService->addFile($link, $destination)) {
                    $mangaTome->setCover($cheminDossierManga."/".$nomFichierImage);
                } else {
                    $mangaTome->setCover(null);
                }
            }

            $managerRegistry->getManager()->persist($mangaTome);
            $managerRegistry->getManager()->flush();

            $this->addFlash('success', 'Tome ajouté');

            return $this->redirectToRoute('manga_tome_add', [
                'id' => $mangaTome->getManga()->getId(),
            ]);
        }
        return $this->render('manga/manga_tome/add.html.twig', [
            'form' => $form->createView(),
            'idManga' => $id,
            'controller_name' => 'MangaController',
            'navLinkId' => 'manga_tome',
        ]);
    }

    #[Route('manga/tome/edit/{id}', name: 'manga_tome_edit')]
    public function edit(ManagerRegistry $managerRegistry, Request $request, MangaTomeRepository $mangaTomeRepository, FileService $fileService, int $id): Response
    {

        $mangaTome = $mangaTomeRepository->findOneBy(['id' => $id]);

        if(!$mangaTome){

            $this->addFlash('error', 'Pas de manga');

            return $this->redirectToRoute('manga');

        }

        $form = $this->createForm(MangaTomeType::class, $mangaTome);

        $form->get('cover')->setData("http" . ($_SERVER['HTTPS'] ? 's' : ''). "://" .$_SERVER['SERVER_NAME'] . $mangaTome->getCover());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            // Lien de l'image à télécharger
            $link = $mangaTome->getCover();

            if ($link && (!str_starts_with($link, "http://") || !str_starts_with($link, "https://"))) {

                // Chemin où enregistrer l'image
                $cheminDossierManga = "/public/image/manga/cover/" . $mangaTome->getManga()->getSlug();

                // Création du dossier s'il n'existe pas
                if (!file_exists($cheminDossierManga)) {
                    mkdir($cheminDossierManga, 0777, true);
                }

                // Nom de fichier pour l'image (peut être personnalisé si nécessaire)
                $nomFichierImage = $mangaTome->getManga()->getSlug().$mangaTome->getTomeNumber().'.jpeg';

                // Chemin complet de destination pour enregistrer l'image
                $destination = $cheminDossierManga . "/" . $nomFichierImage;

                // Téléchargement et enregistrement de l'image
                if ($fileService->addFile($link, $destination)) {
                    $mangaTome->setCover($cheminDossierManga . "/" . $nomFichierImage);
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

        return $this->render('manga/manga_tome/edit.html.twig', [
            'mangaTome' => $mangaTome,
            'form' => $form->createView(),
            'controller_name' => 'MangaController',
            'navLinkId' => 'manga_tome',
        ]);
    }

    #[Route('manga/tome/start/{id}', name: 'manga_tome_start')]
    public function start(ManagerRegistry $managerRegistry, MangaTomeRepository $mangaTomeRepository, int $id): Response
    {
        $tome = $mangaTomeRepository->findOneBy(['id' => $id]);

        if(!$tome){

            $this->addFlash('error', 'Pas de tome');

            return $this->redirectToRoute('manga');

        }

        if ($tome->getReadingStartDate()){

            $this->addFlash('error', 'Manga déjà commencé');

            return $this->redirectToRoute('manga_details', [
                'id' => $tome->getManga()->getId(),
            ]);
        }

        $tome->setReadingStartDate(new DateTime());

        $managerRegistry->getManager()->persist($tome);
        $managerRegistry->getManager()->flush();

        $this->addFlash('success', 'Manga commencé');

        return $this->redirectToRoute('manga_details', [
            'id' => $tome->getManga()->getId(),
        ]);
    }

    #[Route('manga/tome/read/{id}', name: 'manga_tome_read')]
    public function read(ManagerRegistry $managerRegistry, MangaTomeRepository $mangaTomeRepository, int $id): Response
    {
        $tome = $mangaTomeRepository->findOneBy(['id' => $id]);

        if(!$tome){

            $this->addFlash('error', 'Pas de tome');

            return $this->redirectToRoute('manga');

        }

        if ($tome->getReadingEndDate()){

            $this->addFlash('error', 'Manga déjà terminé');

            return $this->redirectToRoute('manga_details', [
                'id' => $tome->getManga()->getId(),
            ]);
        }

        $tome->setReadingEndDate(new DateTime());

        $managerRegistry->getManager()->persist($tome);
        $managerRegistry->getManager()->flush();

        $this->addFlash('success', 'Manga terminé');

        return $this->redirectToRoute('manga_details', [
            'id' => $tome->getManga()->getId(),
        ]);
    }

    #[Route('manga/tome/delete/{id}', name: 'manga_tome_delete')]
    public function delete(ManagerRegistry $managerRegistry, MangaTomeRepository $mangaTomeRepository, int $id): Response
    {
        $tome = $mangaTomeRepository->findOneBy(['id' => $id]);

        if(!$tome){

            $this->addFlash('error', 'Pas de tome');

            return $this->redirectToRoute('manga');

        }

        $managerRegistry->getManager()->remove($tome);
        $managerRegistry->getManager()->flush();

        return $this->redirectToRoute('manga_details', [
            'id' => $tome->getManga()->getId(),
        ]);
    }
}
