<?php

namespace App\Controller\Game;

use App\Entity\Game;
use App\Entity\GameDeveloper;
use App\Entity\GameGenre;
use App\Entity\GameMode;
use App\Entity\GamePlatform;
use App\Entity\GamePublishers;
use App\Entity\GameTheme;
use App\Entity\GameSerie;
use App\Form\GameAddType;
use App\Form\GameType;
use App\Repository\GameDeveloperRepository;
use App\Repository\GameGenreRepository;
use App\Repository\GamePlatformRepository;
use App\Repository\GamePublishersRepository;
use App\Repository\GameRepository;
use App\Repository\GameModeRepository;
use App\Repository\GameSerieRepository;
use App\Repository\GameThemeRepository;
use App\Service\ApiService;
use App\Service\StrSpecialCharsLower;
use App\Service\TimeService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'game', methods: ['GET'])]
    public function index(): Response
    {

        return $this->render('game/game/index.html.twig', [
            'navLinkId' => 'game'
        ]);
    }


    #[Route('/details/{id}', name: 'game_details', methods: ['GET'])]
    public function show(GameRepository $gameRepository, $id): Response
    {

        $game = $gameRepository->find($id);

        return $this->render('game/game/details.html.twig', [
            'game' => $game,
            'navLinkId' => 'game'
        ]);
    }


    /*
        #[Route('/{id}/edit', name: 'game_edit', methods: ['GET', 'POST'])]
        public function edit(Request $request, EntityManagerInterface $entityManager, GameRepository $gameRepository, $id): Response
        {

            $game = $gameRepository->find($id);

            $form = $this->createForm(GameType::class, $game);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('game_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('game/game/edit.html.twig', [
                'game' => $game,
                'form' => $form,
                'navLinkId' => 'game'
            ]);
        }
    */


    #[Route('/delete/{id}', name: 'game_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, GameRepository $gameRepository, $id): Response
    {

        $game = $gameRepository->find($id);

        $gameTrackers = $game->getGameTrackers();

        foreach ($gameTrackers as $gameTracker) {

            $entityManager->remove($gameTracker);

        }

        $entityManager->remove($game);

        $entityManager->flush();

        return $this->redirectToRoute('game', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @throws GuzzleException
     */
    #[Route('/add', name: 'game_add', methods: ['GET', 'POST'])]
    public function add(
        Request                  $request,
        EntityManagerInterface   $entityManager,
        GameModeRepository       $gameModeRepository,
        GameGenreRepository      $gameGenreRepository,
        GameThemeRepository      $gameThemeRepository,
        GamePublishersRepository $gamePublishersRepository,
        GameDeveloperRepository  $gameDeveloperRepository,
        GamePlatformRepository   $gamePlatformRepository,
        GameSerieRepository      $gameSerieRepository,
        StrSpecialCharsLower     $strSpecialCharsLower,
        ApiService               $apiService,
        KernelInterface          $kernel
    ): Response
    {

        $form = $this->createForm(GameAddType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();

            $platformId = $formData['platforms']->getIgdbID();
            $idGame = $formData['igdbId'];

            $game = new Game();

            // NAME

            $body = 'fields name,game_modes,genres,themes; where id = '.$idGame.';';

            $dataGame = $apiService->igdbCall('games', $body);

            $game->setName($dataGame['name']);

            $idParent = null;

            if (isset($dataGame['version_parent'])) {
                $idParent = $dataGame['version_parent'];
            }

            // ALTERNATIVE NAMES

            $body = 'fields *;where game = '.$idGame.' & comment = "French title";';

            $dataName = $apiService->igdbCall('alternative_names', $body);

            if ($dataName !== []) {
                $game->setName($dataName[0]['name']);
            }

            $game->setSlug($strSpecialCharsLower->serie($game->getName()));

            // GAME MODE

            foreach ($dataGame['game_modes'] as $gameModeId) {
                $gameMode = $gameModeRepository->findOneBy(['igdbId' => $gameModeId]);

                if (!$gameMode) {

                    $body = 'fields name;where id = '.$gameModeId.';';

                    $dataGameMode = $apiService->igdbCall('game_modes', $body);

                    $gameMode = new GameMode();

                    $gameMode->setIgdbId($gameModeId);
                    $gameMode->setName($dataGameMode['name']);

                    $entityManager->persist($gameMode);
                    $entityManager->flush();

                }

                $game->addMode($gameMode);
            }

            // GENRES

            foreach ($dataGame['genres'] as $genreId) {
                $genre = $gameGenreRepository->findOneBy(['igdbId' => $genreId]);

                if (!$genre) {

                    $body = 'fields name;where id = '.$genreId.';';

                    $dataGenre = $apiService->igdbCall('genres', $body);

                    $genre = new GameGenre();

                    $genre->setIgdbId($genreId);
                    $genre->setName($dataGenre['name']);

                    $entityManager->persist($genre);
                    $entityManager->flush();

                }

                $game->addGenre($genre);

            }

            // THEME

            foreach ($dataGame['themes'] as $themeId) {
                $theme = $gameThemeRepository->findOneBy(['igdbId' => $themeId]);

                if (!$theme) {

                    $body = 'fields name;where id = '.$themeId.';';

                    $dataTheme = $apiService->igdbCall('themes', $body);

                    $theme = new GameTheme();

                    $theme->setIgdbId($themeId);
                    $theme->setName($dataTheme['name']);

                    $entityManager->persist($theme);
                    $entityManager->flush();

                }

                $game->addTheme($theme);

            }

            // RELEASE DATE

            $body = 'fields *;where game = '.$idGame.' & platform = '.$platformId.' & (status=6 | status = null) & (region=1 | region=8);';

            $dataReleaseDate = $apiService->igdbCall('release_dates', $body);
            $date = new DateTime();
            $date->setTimestamp($dataReleaseDate['date']);

            $game->setReleaseDate($date);

            // PUBLISHER

            $body = 'fields name;where published = ['.$idGame.'];';

            $dataPublisher = $apiService->igdbCall('companies', $body);

            foreach ($dataPublisher as $onePublisher) {

                $publisher = $gamePublishersRepository->findOneBy(['igdbId' => $onePublisher['id']]);

                if (!$publisher) {

                    $publisher = new GamePublishers();

                    $publisher->setName($onePublisher['name']);
                    $publisher->setIgdbId($onePublisher['id']);

                    $entityManager->persist($publisher);
                    $entityManager->flush();

                }

                $game->addPublisher($publisher);

            }

            // DEVELOPER

            $body = 'fields name;where developed = ['.$idGame.'];';

            $dataDeveloper = $apiService->igdbCall('companies', $body);

            foreach ($dataDeveloper as $oneDeveloper) {

                $developer = $gameDeveloperRepository->findOneBy(['igdbId' => $oneDeveloper['id']]);

                if (!$developer) {

                    $developer = new GameDeveloper();

                    $developer->setName($oneDeveloper['name']);
                    $developer->setIgdbId($oneDeveloper['id']);

                    $entityManager->persist($developer);
                    $entityManager->flush();

                }

                $game->addDeveloper($developer);

            }

            // SERIES

            $body = 'fields id,name,games;where games = ['.$idGame.'];';

            $dataSeries = $apiService->igdbCall('collections', $body);

            if (empty($dataSeries) && $idParent) {
                $body = 'fields id,name,games;where games = ['.$idParent.'];';

                $dataSeries = $apiService->igdbCall('collections', $body);
            }

            $saveSeries = null;
            $countGamesSeries = 0;

            foreach ($dataSeries as $series) {

                if (count($series['games']) > $countGamesSeries) {
                    $saveSeries = $series;
                }

            }

            if ($saveSeries) {
                $serie = $gameSerieRepository->findOneBy(['igdbId' => $saveSeries['id']]);

                if (!$serie) {

                    $serie = new GameSerie();

                    $serie->setName($saveSeries['name']);
                    $serie->setIgdbId($saveSeries['id']);

                    $entityManager->persist($serie);
                    $entityManager->flush();

                }

                $game->setSerie($serie);
            }

            //PLATFORM

            $platform = $gamePlatformRepository->findOneBy(['igdbId' => $platformId]);

            if (!$platform) {

                $body = 'fields name;where id = '.$platformId.';';

                $dataPlatform = $apiService->igdbCall('platforms', $body);

                $platform = new GamePlatform();

                $platform->setName($dataPlatform['name']);
                $platform->setIgdbId($platformId);

                $entityManager->persist($platform);
                $entityManager->flush();
            }

            // COVER

            $body = 'fields *;where game = '.$idGame.';';

            $dataGameCover = $apiService->igdbCall('covers', $body);

            $lienImage = 'https:'.str_replace('/t_thumb/', '/t_cover_big/', $dataGameCover['url']);

            $cover = imagecreatefromstring(file_get_contents($lienImage));

            $projectDir = $kernel->getProjectDir();

            $cheminImageDestination = "/public/image/game/cover/".$game->getSlug().'.jpeg';

            if (imagejpeg($cover, $projectDir.$cheminImageDestination, 100)) {
                $game->setCover($cheminImageDestination);
            } else {
                $game->setCover(null);
            }

            $game->addPlatform($platform);
            $game->setIgdbId($idGame);

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('game_details', ['id' => $game->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('game/game/new.html.twig', [
            'form' => $form,
            'navLinkId' => 'game'
        ]);
    }


    #[Route('/list', name: 'game_list', methods: ['POST'])]
    public function list(
        Request        $request,
        GameRepository $gameRepository,
        TimeService    $timeService
    ): Response
    {

        $choice = $request->request->get('choice', 1);
        $text = $request->request->get('text');

        $choice = intval($choice);

        $games = match ($choice) {
            2 => $gameRepository->findGameNotStart($text),
            3 => $gameRepository->findGameProgress($text),
            4 => $gameRepository->findGameEnd($text),
            5 => $gameRepository->findGameFullEnd($text),
            default => $gameRepository->findAllFilter($text),
        };

        $gamesInfo = [];

        foreach ($games as $game) {

            $tooltip = "<ul>";

            if ($game->getSerie()) {
                $tooltip .= "<li>Série : ".$game->getSerie()->getName()."</li>";
            }

            $tooltip .= "<li>Date de sortie : ".$timeService->frenchFormatDateNoDay($game->getReleaseDate())."</li>";

            if ($game->getGameTrackers() && $game->getGameTrackers()->getValues()) {

                $gameTracker = $game->getGameTrackers()->getValues()[0];

                if ($gameTracker->getStartDate()) {
                    $tooltip .= "<li>Commencer le : ".$timeService->frenchFormatDateNoDay($gameTracker->getStartDate())."</li>";
                }

                if ($gameTracker->getEndDate()) {
                    $tooltip .= "<li>Fini le : ".$timeService->frenchFormatDateNoDay($gameTracker->getEndDate())."</li>";
                }

                if ($gameTracker->getEndTime()) {
                    $tooltip .= "<li>Fini en : ".$timeService->convertirSecondes($gameTracker->getEndTime())."</li>";
                }

                if ($gameTracker->getCompleteDate()) {
                    $tooltip .= "<li>Fini à 100% le : ".$timeService->frenchFormatDateNoDay($gameTracker->getCompleteDate())."</li>";
                }

                if ($gameTracker->getCompleteTime()) {
                    $tooltip .= "<li>Fini à 100% en : ".$timeService->convertirSecondes($gameTracker->getCompleteTime())."</li>";
                }

            }

            $tooltip .= "</ul>";

            $gamesInfo[] = [
                "entity" => $game,
                "tooltip" => $tooltip,
            ];

        }

        return $this->render(
            'game/game/list.html.twig',
            [
                'gamesInfo' => $gamesInfo
            ]
        );
    }

}
