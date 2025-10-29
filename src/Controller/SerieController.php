<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Serie;
use App\Form\SerieType;
use App\Form\SerieEditType;
use App\Form\SerieAnimeEditType;
use App\Repository\AnimeGenreRepository;
use App\Repository\AnimeThemeRepository;
use App\Repository\CompanyRepository;
use App\Repository\EpisodeShowRepository;
use App\Repository\SerieRepository;
use App\Repository\EpisodeRepository;
use App\Repository\SerieTypeRepository;
use App\Service\TVDBService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StrSpecialCharsLower;

#[Route('/serie')]
class SerieController extends AbstractController
{


    #[Route('/detail/{id}', name: 'serie_detail')]
    public function detail(Request $request, RequestStack $requestStack, SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository, int $id): Response
    {

        $serie = $serieRepository->findOneBy(['id' => $id]);

        if(!$serie){

            $this->addFlash('error', 'Série non trouvée');

            return $this->redirectToRoute('serie');

        }

        $referer = $request->headers->get('referer');
        $idSerie = null;
        $session = $requestStack->getSession();

        if ($session->get('idSerie')) {
            $idSerie = $session->get('idSerie');
        }

        if ($referer && !$idSerie && str_contains($referer, "http://localhost:8000/serie/") && (!str_contains($referer, "edit") || !str_contains($referer, "detail"))) {
            $idSerie = str_replace('http://localhost:8000/serie/', '', $referer);

            $idSerie = str_replace('detail/', '', $idSerie);

            $session->set('idSerie', $idSerie);
        }

        $idSerie = str_replace('detail/', '', $idSerie);
        $totalDuration = $episodeShowRepository->getDurationBySerie($id);
        $countEpisode = $episodeShowRepository->getCountBySerie($id);
        $episodesShow = $episodeShowRepository->getEpisodesBySerie($serie);

        $studios = [];
        $networks = [];

        foreach ($serie->getCompany()->getValues() as $company) {
            if ($company->getType() === "Studio") {
                $studios[] = $company;
            } else if ($company->getType() === "Network") {
                $networks[] = $company;
            }
        }

        $genres = $serie->getGenres();
        $animeGenres = $serie->getAnimeGenres();
        $animeThemes = $serie->getAnimeThemes();

        $tagTypes = [];

        foreach ($serie->getTags() as $tag) {

            if ($tag->getTagsType()->getNameFra() && !array_key_exists($tag->getTagsType()->getNameFra(), $tagTypes)) {
                $tagTypes[$tag->getTagsType()->getNameFra()] = [];
            }

            $tagTypes[$tag->getTagsType()->getNameFra()][] = $tag;
        }

        return $this->render('serie/details.html.twig', [
            'serie' => $serie,
            'totalDuration' => $totalDuration['COUNT'],
            'countEpisode' => $countEpisode['COUNT'],
            'episodesShow' => $episodesShow,
            'genres' => $genres,
            'tagTypes' => $tagTypes,
            'animeGenres' => $animeGenres,
            'animeThemes' => $animeThemes,
            'idSerie' => $idSerie,
            'studios' => $studios,
            'networks' => $networks,
            'navLinkId' => 'serie_list',
        ]);
    }


    #[Route('/add', name: 'serie_add')]
    public function addSerie(ManagerRegistry $managerRegistry, Request $request, StrSpecialCharsLower $strSpecialCharsLower, TVDBService $TVDBService): Response
    {

        $serie = new Serie();

        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($serie->getTvdbId()) {
                $TVDBService->updateSerieName($serie);
            }

            $serie->setSlug($strSpecialCharsLower->serie($serie->getName()));

            if ($serie->getTvdbId()) {
                $TVDBService->updateArtwork($serie);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('episode');
        }

        return $this->render('serie/add.html.twig', [
            'form' => $form->createView(),
            'navLinkId' => 'serie_add',
        ]);
    }


    #[Route('/edit/{id}', name: 'serie_edit')]
    public function editSerie(ManagerRegistry $managerRegistry, SerieRepository $serieRepository, Request $request, int $id): Response
    {

        $serie = $serieRepository->findOneBy(['id' => $id]);

        if(!$serie){

            $this->addFlash('error', 'Série non trouvée');

            return $this->redirectToRoute('serie');

        }

        if ($serie->getSerieType()->getName() === "Anime") {
            return $this->redirectToRoute('serie_edit_anime', ['id' => $id]);
        }

        $form = $this->createForm(SerieEditType::class, $serie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('serie_detail', ['id' => $id]);
        }

        return $this->render('serie/edit.html.twig', [
            'form' => $form->createView(),
            'serie' => $serie,
            'navLinkId' => 'serie_add',
        ]);
    }


    #[Route('/editAnime/{id}', name: 'serie_edit_anime')]
    public function editSerieAnime(ManagerRegistry $managerRegistry, SerieRepository $serieRepository, Request $request, int $id): Response
    {

        $serie = $serieRepository->findOneBy(['id' => $id]);

        if(!$serie){

            $this->addFlash('error', 'Série non trouvée');

            return $this->redirectToRoute('serie');

        }

        if ($serie->getSerieType()->getName() !== "Anime") {
            return $this->redirectToRoute('serie_edit', ['id' => $id]);
        }

        $oldAnimeGenre = $serie->getAnimeGenres()->getValues();
        $oldAnimeTheme = $serie->getAnimeThemes()->getValues();

        $form = $this->createForm(SerieAnimeEditType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $animeGenres = $serie->getAnimeGenres()->getValues();

            foreach ($oldAnimeGenre as $genre) {
                $serie->addAnimeGenre($genre);
                $serie->removeAnimeGenre($genre);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            foreach ($animeGenres as $genre) {
                $serie->removeAnimeGenre($genre);
                $serie->addAnimeGenre($genre);
            }

            $animeThemes = $serie->getAnimeThemes()->getValues();

            foreach ($oldAnimeTheme as $theme) {
                $serie->addAnimeTheme($theme);
                $serie->removeAnimeTheme($theme);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            foreach ($animeThemes as $theme) {
                $serie->removeAnimeTheme($theme);
                $serie->addAnimeTheme($theme);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('serie_detail', ['id' => $id]);
        }

        return $this->render('serie/editAnime.html.twig', [
            'form' => $form->createView(),
            'serie' => $serie,
            'navLinkId' => 'serie_add',
        ]);
    }


    #[Route('/genre/{name}', name: 'serie_genre')]
    public function animeByGenre(EpisodeShowRepository $episodeRepository, AnimeGenreRepository $animeGenreRepository, string $name): Response
    {

        $animeGenre = $animeGenreRepository->findOneBy(['name' => $name]);

        if(!$animeGenre){

            $this->addFlash('error', 'Genre non trouvé');

            return $this->redirectToRoute('serie');

        }

        $series = $animeGenre->getSerie();

        $serieTab = $this->serieTab($series, $episodeRepository);

        return $this->render('serie/index.html.twig', [
            'series' => $serieTab,
            'navLinkId' => 'serie_list',
        ]);
    }


    /**
     * @param Collection<int, Serie> $series
     * @param EpisodeShowRepository  $episodeRepository
     *
     * @return array<int<0, max>, array<string,mixed>>
     * @throws NonUniqueResultException
     */
    private function serieTab(Collection $series, EpisodeShowRepository $episodeRepository): array
    {

        $serieTab = [];

        foreach ($series as $serie) {

            $lastEpisode = $episodeRepository->findLastEpisodeBySerie($serie);

            $serieTab[] = [
                'id' => $serie->getId(),
                'name' => $serie->getName(),
                'serieType' => $serie->getSerieType()->getName(),
                'artwork' => $serie->getArtwork(),
                'lastDate' => $lastEpisode?->getShowDate(),
                'entity' => $serie,
            ];

        }

        uasort($serieTab, function ($a, $b) {

            // Utilise strtotime pour convertir les dates en timestamps pour une comparaison facile
            $dateA = $a['lastDate'];
            $dateB = $b['lastDate'];

            // Retourne -1 si $dateA est inférieur à $dateB, 1 si supérieur, 0 si égal
            return $dateB <=> $dateA;
        });

        return $serieTab;

    }


    #[Route('/theme/{name}', name: 'serie_theme')]
    public function animeByTheme(EpisodeShowRepository $episodeRepository, AnimeThemeRepository $animeThemeRepository, string $name): Response
    {

        $animeTheme = $animeThemeRepository->findOneBy(['name' => $name]);

        if(!$animeTheme){

            $this->addFlash('error', 'Thème non trouvé');

            return $this->redirectToRoute('serie');

        }

        $series = $animeTheme->getSerie();

        $serieTab = $this->serieTab($series, $episodeRepository);

        return $this->render('serie/index.html.twig', [
            'series' => $serieTab,
            'navLinkId' => 'serie_list',
        ]);
    }


    /**
     * @throws NonUniqueResultException
     */
    #[Route('/company/{id}', name: 'serie_company')]
    public function serieByCompany(EpisodeShowRepository $episodeShowRepository, CompanyRepository $companyRepository, int $id): Response
    {

        $company = $companyRepository->find($id);

        if(!$company){

            $this->addFlash('error', 'Entreprise non trouvée');

            return $this->redirectToRoute('serie');

        }

        $serieTab = [];

        foreach ($company->getSeries() as $serie) {

            $lastEpisode = $episodeShowRepository->findLastEpisodeBySerie($serie);

            $serieTab[] = [
                'id' => $serie->getId(),
                'name' => $serie->getName(),
                'serieType' => $serie->getSerieType()->getName(),
                'artwork' => $serie->getArtwork(),
                'lastDate' => $lastEpisode?->getShowDate(),
                'entity' => $serie,
            ];

        }

        uasort($serieTab, function ($a, $b) {

            // Utilise strtotime pour convertir les dates en timestamps pour une comparaison facile
            $dateA = $a['lastDate'];
            $dateB = $b['lastDate'];

            // Retourne -1 si $dateA est inférieur à $dateB, 1 si supérieur, 0 si égal
            return $dateB <=> $dateA;
        });

        return $this->render('serie/index.html.twig', [
            'id' => -2,
            'companyName' => $company->getName(),
            'series' => $serieTab,
            'navLinkId' => 'serie_list',
        ]);
    }


    /**
     * @throws NonUniqueResultException
     */
    #[Route('/list', name: 'serie_list')]
    public function serieList(SerieRepository $serieRepository, SerieTypeRepository $serieTypeRepository, EpisodeShowRepository $episodeShowRepository, CompanyRepository $companyRepository, Request $request): Response
    {

        $id = $request->request->get('id');
        /** @var ?string $text */
        $text = $request->request->get('text');

        if ($id < -1) {

            /** @var string $companyName */
            $companyName = $request->request->get('company');

            $companyName = html_entity_decode($companyName);

            /** @var Company $company */
            $company = $companyRepository->findOneBy(['name' => $companyName]);

            $series = $serieRepository->getSeriesByCompany($company, $text);

        } else if ($id < 0) {
            $series = $serieRepository->search(null, $text);

        } else if ($id == 404) {

            $series = $serieRepository->noThemeGenre($text);

        } else {

            $serieType = $serieTypeRepository->find($id);

            $series = $serieRepository->search($serieType, $text);
        }

        $serieTab = [];

        foreach ($series as $serie) {

            $lastEpisode = $episodeShowRepository->findLastEpisodeBySerie($serie);

            $serieTab[] = [
                'id' => $serie->getId(),
                'name' => $serie->getName(),
                'serieType' => $serie->getSerieType()->getName(),
                'artwork' => $serie->getArtwork(),
                'lastDate' => $lastEpisode?->getShowDate(),
                'entity' => $serie,
            ];

        }

        uasort($serieTab, function ($a, $b) {

            // Utilise strtotime pour convertir les dates en timestamps pour une comparaison facile
            $dateA = $a['lastDate'];
            $dateB = $b['lastDate'];

            // Retourne -1 si $dateA est inférieur à $dateB, 1 si supérieur, 0 si égal
            return $dateB <=> $dateA;
        });

        return $this->render('serie/list.html.twig', [
            'series' => $serieTab,
            'navLinkId' => 'serie_list',
        ]);
    }


    #[Route('/{search}', name: 'serie')]
    public function index(string $search = null): Response
    {

        $id = match ($search) {
            'Animes' => 1,
            'Replay' => 2,
            'Séries' => 3,
            'Dessins_Animés' => 4,
            'A_Traiter' => 404,
            default => -1
        };

        return $this->render('serie/index.html.twig', [
            'id' => $id,
            'navLinkId' => 'serie_list',
        ]);

    }
}
