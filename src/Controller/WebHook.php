<?php

namespace App\Controller;

use App\Entity\EpisodeShow;
use App\Entity\Movie;
use App\Entity\MovieShow;
use App\Entity\SerieType;
use App\Repository\MovieRepository;
use App\Repository\SerieTypeRepository;
use App\Service\AniListService;
use App\Service\StrSpecialCharsLower;
use App\Service\TMDBService;
use App\Service\TVDBService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;
use App\Repository\SerieRepository;
use App\Repository\EpisodeRepository;
use App\Entity\Serie;
use App\Entity\Episode;

class WebHook extends AbstractController
{

    #[Route('/webhook', name: 'webhook')]
    public function webhook(
        UsersRepository      $usersRepository,
        SerieRepository      $serieRepository,
        EpisodeRepository    $episodeRepository,
        MovieRepository      $movieRepository,
        StrSpecialCharsLower $strSpecialCharsLower,
        SerieTypeRepository  $serieTypeRepository,
        TMDBService          $TMDBService,
        TVDBService          $TVDBService,
        AniListService       $aniListService,
        ManagerRegistry      $managerRegistry
    ): Response
    {

        $em = $managerRegistry->getManager();
        $payload = $_POST['payload'];

        $jsonData = json_decode($payload, true);

        if (stripos($jsonData['event'], 'media') !== FALSE) {

            $user = $usersRepository->findOneBy(['plexName' => $jsonData['Account']['title']]);

            if (!$user) {

                return new Response('FALSE');

            }

            $type = str_replace(['Quasinas ', ' A Deux', ' Chat', ' Doudou'], ['', '', '', ''], $jsonData['Metadata']['librarySectionTitle']);

            if ($type === "Films") {
                if ($jsonData['event'] === "media.scrobble") {

                    $movieId = str_replace(["plex://movie/"], [""], $jsonData['Metadata']['guid']);

                    $movie = $movieRepository->findOneBy(['plexId' => $movieId, 'user' => $user]);

                    if (!$movie) {

                        $tvdbMovieId = null;

                        if (isset($jsonData['Metadata']['Guid'])) {
                            foreach ($jsonData['Metadata']['Guid'] as $guid) {
                                if (isset($guid['id']) && str_starts_with($guid['id'], 'tmdb://')) {
                                    $tvdbMovieId = intval(str_replace(["tmdb://"], [""], $guid['id']));
                                    break;
                                }
                            }
                        }

                        $movie = new Movie();

                        $movie->setPlexId($movieId);
                        $movie->setUser($user);
                        $movie->setTmdbId($tvdbMovieId);
                        $movie->setDuration($jsonData['Metadata']['duration'] ?? null);

                        if ($movie->getTmdbId()) {
                            $TMDBService->updateInfo($movie);
                        } else {
                            $movie->setName($jsonData['Metadata']['title']);
                            $movie->setSlug($strSpecialCharsLower->serie($jsonData['Metadata']['title']));
                        }

                        $em->persist($movie);
                        $em->flush();
                    }

                    $movieShow = new MovieShow();
                    $movieShow->setMovie($movie);
                    $movieShow->setShowDate(new DateTime());

                    $em->persist($movieShow);
                    $em->flush();
                }

            } else {

                $serieId = str_replace(["plex://show/"], [""], $jsonData['Metadata']['grandparentGuid']);

                if (isset($jsonData['Metadata']['Guid'])) {
                    foreach ($jsonData['Metadata']['Guid'] as $guid) {
                        if (isset($guid['id']) && str_starts_with($guid['id'], 'tvdb://')) {
                            $episodeId = str_replace(["tvdb://"], [""], $guid['id']);
                            break;
                        }
                    }
                }

                $serie = $serieRepository->findOneBy(['plexId' => $serieId]);

                if (!$serie and isset($episodeId)) {
                    $tvdbSerieId = $TVDBService->getSerieIdByEpisodeId($episodeId);

                    $serie = $serieRepository->findOneBy(['tvdbId' => $tvdbSerieId]);
                }

                $serieType = $serieTypeRepository->findOneBy(['name' => $type]);

                if (!$serieType) {
                    $serieType = new SerieType();

                    $serieType->setName($type);

                    $em->persist($serieType);
                    $em->flush();
                }

                if (!$serie) {
                    $serie = new Serie;

                    $serie->setPlexId($serieId);
                    $serie->setName($jsonData['Metadata']['grandparentTitle']);
                    $serie->setSerieType($serieType);

                    $serie->setSlug($strSpecialCharsLower->serie($serie->getName()));

                    $em->persist($serie);
                    $em->flush();

                    if ($serie->getSerieType()->getName() === "Anime") {
                        $aniListService->setScore($serie);
                    }
                }

                if ($jsonData['event'] === "media.scrobble") {

                    $episodeId = null;
                    $episode = null;

                    if (isset($jsonData['Metadata']['guid'])) {
                        /** @var ?string $episodeId */
                        $episodeId = str_replace(["plex://episode/", "local://"], ["", ""], $jsonData['Metadata']['guid']);

                        $episode = $episodeRepository->findOneBy(['serie' => $serie, 'saisonNumber' => $jsonData['Metadata']['parentIndex'], 'episodeNumber' => $jsonData['Metadata']['index']]);
                    }

                    if (!$episode) {

                        $tvdbId = null;

                        if (isset($jsonData['Metadata']['Guid'])) {
                            foreach ($jsonData['Metadata']['Guid'] as $guid) {
                                if (isset($guid['id']) && str_starts_with($guid['id'], 'tvdb://')) {
                                    $tvdbId = intval(str_replace(["tvdb://"], [""], $guid['id']));
                                    break;
                                }
                            }
                        }

                        $episode = new Episode;

                        $episode->setTvdbId($tvdbId);

                        if ($episode->getTvdbId()) {
                            $TVDBService->updateEpisodeName($episode);

                            if (!$jsonData['Metadata']['duration']) {
                                $TVDBService->updateEpisodeDuration($episode);
                            } else {
                                $episode->setDuration($jsonData['Metadata']['duration']);
                            }

                        } else {
                            $episode->setName($jsonData['Metadata']['title']);
                            $episode->setDuration($jsonData['Metadata']['duration'] ?? null);
                        }

                        $episode->setPlexId($episodeId);
                        $episode->setUser($user);
                        $episode->setSaison($jsonData['Metadata']['parentTitle']);
                        $episode->setSaisonNumber($jsonData['Metadata']['parentIndex']);
                        $episode->setEpisodeNumber($jsonData['Metadata']['index']);

                        if ($episode->getTvdbId() && !$serie->getTvdbId()) {
                            $serie->setTvdbId($TVDBService->getSerieIdByEpisodeId($episode->getTvdbId()));

                            $TVDBService->updateSerieInfo($serie);

                            $em->persist($serie);
                        }

                        $episode->setSerie($serie);

                        $em->persist($episode);
                        $em->flush();
                    }

                    $episodeShow = new EpisodeShow();
                    $episodeShow->setEpisode($episode);
                    $episodeShow->setShowDate(new DateTime());

                    $em->persist($episodeShow);
                    $em->flush();

                }
            }
        }

        return new Response('OK');
    }

}
	