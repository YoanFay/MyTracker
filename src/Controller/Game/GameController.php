<?php

namespace App\Controller\Game;

use App\Entity\Game;
use App\Entity\GameDeveloper;
use App\Entity\GameGenre;
use App\Entity\GameMode;
use App\Entity\GamePlatform;
use App\Entity\GamePublishers;
use App\Entity\GameTheme;
use App\Form\GameType;
use App\Repository\GameDeveloperRepository;
use App\Repository\GameGenreRepository;
use App\Repository\GamePlatformRepository;
use App\Repository\GamePublishersRepository;
use App\Repository\GameRepository;
use App\Repository\GameModeRepository;
use App\Repository\GameSerieRepository;
use App\Repository\GameThemeRepository;
use App\Service\StrSpecialCharsLower;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {

        return $this->render('game/game/index.html.twig', [
            'games' => $gameRepository->findAllByName(),
            'navLinkId' => 'game'
        ]);
    }


    #[Route('/new', name: 'game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game/new.html.twig', [
            'game' => $game,
            'form' => $form,
            'navLinkId' => 'game'
        ]);
    }


    #[Route('/{id}/details', name: 'game_show', methods: ['GET'])]
    public function show(GameRepository $gameRepository, $id): Response
    {

        $game = $gameRepository->find($id);

        return $this->render('game/game/show.html.twig', [
            'game' => $game,
            'navLinkId' => 'game'
        ]);
    }


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


    #[Route('/{id}/delete', name: 'game_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, GameRepository $gameRepository, $id): Response
    {

        $game = $gameRepository->find($id);

        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/test', name: 'game_test', methods: ['GET'])]
    public function test(
        Request                  $request,
        EntityManagerInterface   $entityManager,
        GameRepository           $gameRepository,
        GameModeRepository       $gameModeRepository,
        GameGenreRepository      $gameGenreRepository,
        GameThemeRepository      $gameThemeRepository,
        GamePublishersRepository $gamePublishersRepository,
        GameDeveloperRepository  $gameDeveloperRepository,
        GamePlatformRepository   $gamePlatformRepository,
        GameSerieRepository      $gameSerieRepository,
        StrSpecialCharsLower $strSpecialCharsLower,
        KernelInterface $kernel
    ): Response
    {

        $games = [
            [
                'id' => 136498,
                'platform' => 130
            ],
            [
                'id' => 140427,
                'platform' => 6
            ],
            [
                'id' => 7386,
                'platform' => 6
            ],
            [
                'id' => 119171,
                'platform' => 6
            ],
            [
                'id' => 119388,
                'platform' => 130
            ]
        ];

        // AUTHENTIFICATION

        $client = new Client();

        $response = $client->post("https://id.twitch.tv/oauth2/token?client_id=sd5xdt5w2lkjr7ws92fxjdlicvb5u2&client_secret=tymefepntjuva1n9ipa3lkjts2pmdh&grant_type=client_credentials", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        $token = "Bearer ".$data['access_token'];

        foreach ($games as $idGame) {

            $platformId = $idGame['platform'];
            $idGame = $idGame['id'];

            $game = new Game();

            // NAME

            $response = $client->post("https://api.igdb.com/v4/games", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields name,game_modes,genres,themes; where id = '.$idGame.';'
            ]);

            $dataGame = json_decode($response->getBody(), true)[0];

            $game->setName($dataGame['name']);

            $response = $client->post("https://api.igdb.com/v4/alternative_names", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields *;where game = '.$idGame.' & comment = "French title";'
            ]);

            $dataName = json_decode($response->getBody(), true);

            if ($dataName !== []) {
                $game->setName($dataName[0]['name']);
            }

            $game->setSlug($strSpecialCharsLower->serie($game->getName()));

            // GAME MODE

            foreach ($dataGame['game_modes'] as $gameModeId) {
                $gameMode = $gameModeRepository->findOneBy(['imdbId' => $gameModeId]);

                if (!$gameMode) {


                    $response = $client->post("https://api.igdb.com/v4/game_modes", [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                            'Authorization' => $token
                        ],
                        'body' => 'fields name;where id = '.$gameModeId.';'
                    ]);

                    $dataGameMode = json_decode($response->getBody(), true)[0];

                    $gameMode = new GameMode();

                    $gameMode->setImdbId($gameModeId);
                    $gameMode->setName($dataGameMode['name']);

                    $entityManager->persist($gameMode);
                    $entityManager->flush();

                }
            }

            // GENRE

            foreach ($dataGame['genres'] as $genreId) {
                $genre = $gameGenreRepository->find(['imdbId' => $genreId]);

                if (!$genre) {


                    $response = $client->post("https://api.igdb.com/v4/genres", [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                            'Authorization' => $token
                        ],
                        'body' => 'fields name;where id = '.$genreId.';'
                    ]);

                    $dataGenre = json_decode($response->getBody(), true)[0];

                    $genre = new GameGenre();

                    $genre->setImdbId($genreId);
                    $genre->setName($dataGenre['name']);

                    $entityManager->persist($genre);
                    $entityManager->flush();

                }

                $game->addGenre($genre);

            }

            // THEME

            foreach ($dataGame['themes'] as $themeId) {
                $theme = $gameThemeRepository->find(['imdbId' => $themeId]);

                if (!$theme) {

                    $response = $client->post("https://api.igdb.com/v4/themes", [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                            'Authorization' => $token
                        ],
                        'body' => 'fields name;where id = '.$themeId.';'
                    ]);

                    $dataTheme = json_decode($response->getBody(), true)[0];

                    $theme = new GameTheme();

                    $theme->setImdbId($themeId);
                    $theme->setName($dataTheme['name']);

                    $entityManager->persist($theme);
                    $entityManager->flush();

                }

                $game->addTheme($theme);

            }

            // RELEASE DATE

            $response = $client->post("https://api.igdb.com/v4/release_dates", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields name;where id = '.$idGame.' & platform = '.$platformId.' & status=6;'
            ]);

            $dataReleaseDate = json_decode($response->getBody(), true)[0];
            $date = new DateTime();
            $date->setTimestamp($dataReleaseDate['date']);

            $game->setReleaseDate($date);

            // PUBLISHER

            $response = $client->post("https://api.igdb.com/v4/companies", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields name;where published = ['.$idGame.'];'
            ]);

            $dataPublisher = json_decode($response->getBody(), true);

            foreach ($dataPublisher as $onePublisher) {

                $publisher = $gamePublishersRepository->findOneBy(['imdbId' => $onePublisher['id']]);

                if (!$publisher) {

                    $publisher = new GamePublishers();

                    $publisher->setName($onePublisher['name']);
                    $publisher->setImdbId($onePublisher['id']);

                    $entityManager->persist($publisher);
                    $entityManager->flush();

                }

                $game->addPublisher($publisher);

            }

            // DEVELOPER

            $response = $client->post("https://api.igdb.com/v4/companies", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields name;where developed = ['.$idGame.'];'
            ]);

            $dataDeveloper = json_decode($response->getBody(), true);

            foreach ($dataDeveloper as $oneDeveloper) {

                $developer = $gameDeveloperRepository->findOneBy(['imdbId' => $oneDeveloper['id']]);

                if (!$developer) {

                    $developer = new GameDeveloper();

                    $developer->setName($oneDeveloper['name']);
                    $developer->setImdbId($oneDeveloper['id']);

                    $entityManager->persist($developer);
                    $entityManager->flush();

                }

                $game->addDeveloper($developer);

            }

            //SERIES

            $response = $client->post("https://api.igdb.com/v4/collections", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields id,name,games;where games = ['.$idGame.'];'
            ]);

            $dataSeries = json_decode($response->getBody(), true);

            $saveSeries = null;
            $countGamesSeries = 0;

            foreach ($dataSeries as $series) {

                if (count($series['games']) > $countGamesSeries) {
                    $saveSeries = $series;
                }

            }

            $serie = $gameSerieRepository->findOneBy(['imdbId' => $saveSeries['id']]);

            if (!$serie) {

                $serie = new GameDeveloper();

                $serie->setName($saveSeries['name']);
                $serie->setImdbId($saveSeries['id']);

                $entityManager->persist($serie);
                $entityManager->flush();

            }

            $game->setSerie($serie);

            //PLATFORM

            $platform = $gamePlatformRepository->findOneBy(['imdbId' => $platformId]);

            if (!$platform) {

                $response = $client->post("https://api.igdb.com/v4/companies", [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                        'Authorization' => $token
                    ],
                    'body' => 'fields name;where id = '.$platformId.';'
                ]);

                $dataPlatform = json_decode($response->getBody(), true)[0];

                $platform = new GamePlatform();

                $platform->setName($dataPlatform['name']);
                $platform->setImdbId($platformId);

                $entityManager->persist($platform);
                $entityManager->flush();
            }

            // COVER

            $response = $client->post("https://api.igdb.com/v4/covers", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields name;where game = '.$platformId.';'
            ]);

            $dataGameCover = json_decode($response->getBody(), true)[0];

            $lienImage = 'https:'.str_replace('/t_thumb/', '/t_cover_big/', $dataGameCover['url']);;

            $cover = imagecreatefromstring(file_get_contents($lienImage));

            $projectDir = $kernel->getProjectDir();

            $cheminImageDestination = "/public/image/game/cover/" . $game->getSlug().'.jpeg';

            if (imagejpeg($cover, $projectDir . $cheminImageDestination, 100)) {
                $game->setCover($cheminImageDestination);
            } else {
                $game->setCover(null);
            }

            $game->addPlatform($platform);
            $game->setImdbId($idGame);

            $entityManager->persist($platform);
            $entityManager->flush();

        }

        dd('Fin Test');
    }
}
