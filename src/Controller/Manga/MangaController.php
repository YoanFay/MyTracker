<?php

namespace App\Controller\Manga;

use App\Entity\Manga;
use App\Entity\MangaTome;
use App\Form\MangaFormType;
use App\Repository\MangaRepository;
use App\Repository\MangaTomeRepository;
use App\Service\TimeService;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StrSpecialCharsLower;

class MangaController extends AbstractController
{
    #[Route('/manga', name: 'manga')]
    public function index(MangaRepository $mangaRepository, MangaTomeRepository $mangaTomeRepository, TimeService $timeService): Response
    {

        $mangas = $mangaRepository->findBy([], ['name' => 'ASC']);

        $mangasInfo = [];

        foreach ($mangas as $manga) {

            $tomeInfo = $mangaTomeRepository->getTomeCountInfo($manga);

            $firstTomeCover = $mangaTomeRepository->getFirstCover($manga);

            if ($firstTomeCover) {
                $firstTomeCover = $firstTomeCover['COVER'];
            }

            $startDate = $timeService->frenchFormatDateNoDay($manga->getReleaseDate());
            $endDate = "En cours";

            if ($manga->getEndDate()) {
                $endDate = $timeService->frenchFormatDateNoDay($manga->getEndDate());
            }

            $staff = "<li>Auteur : ".$manga->getAuthor()->getName()." </li>";

            if ($manga->getDesigner()) {

                $staff = "<li>Scénariste : ".$manga->getAuthor()->getName()." </li><li>Dessinateur : ".$manga->getDesigner()->getName()." </li>";

            }

            $tooltip = "
                <ul class='text-start'>
                ".$staff."
                <li>Éditeur : ".$manga->getEditor()->getName()." </li>
                <li>Date de publication : ".$startDate." - ".$endDate." </li>
                <li>Tome lu : ".$tomeInfo['tomeRead']." / ".$tomeInfo['tomeRelease']."</li>
                </ul>";

            $mangasInfo[] = [
                'info' => $manga,
                'tomeRead' => $tomeInfo['tomeRead'],
                'tomeRelease' => $tomeInfo['tomeRelease'],
                'firstTomeCover' => $firstTomeCover,
                'tooltip' => $tooltip,
            ];
        }

        return $this->render('manga/manga/index.html.twig', [
            'mangas' => $mangasInfo,
            'navLinkId' => 'manga',
        ]);
    }


    /**
     * @throws NonUniqueResultException
     */
    #[Route('/manga/details/{id}', name: 'manga_details')]
    public function details(MangaRepository $mangaRepository, MangaTomeRepository $mangaTomeRepository, int $id): Response
    {

        $manga = $mangaRepository->findOneBy(['id' => $id]);

        if (!$manga){
            $this->addFlash('error', 'Ce manga n\'existe pas');
            return $this->redirectToRoute('manga');
        }

        $tome = $mangaTomeRepository->getCurrentTome($manga);

        $currentTome = null;

        $firstTomeDate = $mangaTomeRepository->getFirstTomeDate($manga)['DATE'] ?? null;
        $lastTomeDate = $mangaTomeRepository->getLastTomeDate($manga)['DATE'] ?? null;
        $nbTome = $mangaTomeRepository->getTomeCountByManga($manga)['COUNT'] ?? 0;

        if ($tome) {
            $started = false;

            if ($tome->getReadingStartDate()) {
                $started = true;
            }

            $release = false;

            if ($tome->getReleaseDate() <= new DateTime()) {
                $release = true;
            }

            $currentTome = [
                'tomeNumber' => $tome->getTomeNumber(),
                'tomeId' => $tome->getId(),
                'started' => $started,
                'release' => $release,
                'releaseDate' => $tome->getReleaseDate(),
            ];
        }

        return $this->render('manga/manga/details.html.twig', [
            'manga' => $manga,
            'currentTome' => $currentTome,
            'firstTomeDate' => $firstTomeDate,
            'lastTomeDate' => $lastTomeDate,
            'nbTome' => $nbTome,
            'navLinkId' => 'manga',
        ]);
    }


    #[Route('/manga/add', name: 'manga_add')]
    public function add(ManagerRegistry $managerRegistry, Request $request, StrSpecialCharsLower $strSpecialCharsLower): Response
    {

        $manga = new Manga();

        $form = $this->createForm(MangaFormType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manga->setSlug($strSpecialCharsLower->main($manga->getName()));

            $managerRegistry->getManager()->persist($manga);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('manga_details', [
                'id' => $manga->getId()
            ]);
        }

        return $this->render('manga/manga/add.html.twig', [
            'form_title' => 'Ajouter un manga',
            'form' => $form->createView(),
            'navLinkId' => 'manga',
        ]);
    }


    #[Route('/manga/edit/{id}', name: 'manga_edit')]
    public function edit(MangaRepository $mangaRepository, ManagerRegistry $managerRegistry, Request $request, StrSpecialCharsLower $strSpecialCharsLower, int $id): Response
    {

        $manga = $mangaRepository->find($id);

        if (!$manga) {
            $this->addFlash('error', 'Ce manga n\'existe pas');
            return $this->redirectToRoute('manga_add');
        }

        $form = $this->createForm(MangaFormType::class, $manga);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manga->setSlug($strSpecialCharsLower->main($manga->getName()));

            $managerRegistry->getManager()->persist($manga);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('manga_details', [
                'id' => $manga->getId()
            ]);
        }

        return $this->render('manga/manga/edit.html.twig', [
            'id' => $manga->getId(),
            'form_title' => 'Modifier un manga',
            'form' => $form->createView(),
            'navLinkId' => 'manga',
        ]);
    }


    #[Route('/manga/delete/{id}', name: 'manga_delete')]
    public function delete(MangaRepository $mangaRepository, ManagerRegistry $managerRegistry, Request $request, StrSpecialCharsLower $strSpecialCharsLower, int $id): Response
    {

        $em = $managerRegistry->getManager();

        $manga = $mangaRepository->find($id);

        if (!$manga) {
            $this->addFlash('error', 'Ce manga n\'existe pas');
            return $this->redirectToRoute('manga');
        }

        $tomes = $manga->getMangaTomes();

        foreach ($tomes as $tome) {
            $em->remove($tome);
        }

        $em->remove($manga);
        $em->flush();

        $this->addFlash('success', 'Manga suprimé');

        return $this->redirectToRoute('manga');
    }


}
