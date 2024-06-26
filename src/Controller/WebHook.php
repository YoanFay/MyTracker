<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\SerieType;
use App\Repository\MovieRepository;
use App\Repository\SerieTypeRepository;
use App\Service\StrSpecialCharsLower;
use App\Service\TMDBService;
use App\Service\TVDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;
use App\Repository\SerieRepository;
use App\Repository\EpisodeShowRepository;
use App\Entity\Users;
use App\Entity\Serie;
use App\Entity\EpisodeShow;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class WebHook extends AbstractController
{

    /**
     * @Route("/webhook", name="webhook")
     */
    public function webhook(
        Request               $request,
        UsersRepository       $usersRepository,
        SerieRepository       $serieRepository,
        EpisodeShowRepository $episodeShowRepository,
        MovieRepository       $movieRepository,
        StrSpecialCharsLower  $strSpecialCharsLower,
        SerieTypeRepository   $serieTypeRepository,
        TMDBService           $TMDBService,
        TVDBService           $TVDBService
    ): Response
    {

        $em = $this->getDoctrine()->getManager();
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
                                if (isset($guid['id']) && strpos($guid['id'], 'tmdb://') === 0) {
                                    $tvdbMovieId = str_replace(["tmdb://"], [""], $guid['id']);
                                    break;
                                }
                            }
                        }

                        $movie = new Movie();

                        $movie->setPlexId($movieId);
                        $movie->setUser($user);
                        $movie->setShowDate(new \DateTime());
                        $movie->setTmdbId($tvdbMovieId);
                        $movie->setDuration($jsonData['Metadata']['duration'] ?? null);

                        if ($movie->getTmdbId()) {
                            $TMDBService->updateInfo($movie);
                        } else {
                            $movie->setName($jsonData['Metadata']['title']);
                            $movie->setSlug($strSpecialCharsLower->serie($movie->getName()));
                        }

                        $em->persist($movie);
                        $em->flush();
                    }
                }

            } else {

                $serieId = str_replace(["plex://show/"], [""], $jsonData['Metadata']['grandparentGuid']);

                $serie = $serieRepository->findOneBy(['plexId' => $serieId]);

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
                }

                if ($jsonData['event'] === "media.scrobble") {

                    $episodeId = null;
                    $episode = null;

                    if (isset($jsonData['Metadata']['guid'])) {
                        $episodeId = str_replace(["plex://episode/", "local://"], ["", ""], $jsonData['Metadata']['guid']);

                        $episode = $episodeShowRepository->findOneBy(['plexId' => $episodeId, 'user' => $user]);
                    }

                    if (!$episode) {

                        $tvdbId = null;

                        if (isset($jsonData['Metadata']['Guid'])) {
                            foreach ($jsonData['Metadata']['Guid'] as $guid) {
                                if (isset($guid['id']) && strpos($guid['id'], 'tvdb://') === 0) {
                                    $tvdbId = str_replace(["tvdb://"], [""], $guid['id']);
                                    break;
                                }
                            }
                        }

                        $episode = new EpisodeShow;

                        $episode->setTvdbId($tvdbId);

                        if ($episode->getTvdbId()){
                            $TVDBService->updateEpisodeName($episode);

                            if(!$jsonData['Metadata']['duration']){
                                $TVDBService->updateEpisodeDuration($episode);
                            }

                        }else{
                            $episode->setName($jsonData['Metadata']['title']);
                            $episode->setDuration($jsonData['Metadata']['duration'] ?? null);
                        }

                        $episode->setPlexId($episodeId);
                        $episode->setShowDate(new \DateTime());
                        $episode->setUser($user);
                        $episode->setSaison($jsonData['Metadata']['parentTitle']);
                        $episode->setSaisonNumber($jsonData['Metadata']['parentIndex']);
                        $episode->setEpisodeNumber($jsonData['Metadata']['index']);

                        if ($episode->getTvdbId() && !$serie->getTvdbId()){
                            $serie->setTvdbId($TVDBService->getSerieIdByEpisodeId($episode->getTvdbId()));

                            $TVDBService->updateSerieInfo($serie);

                            $em->persist($serie);
                        }

                        $episode->setSerie($serie);

                        $em->persist($episode);
                        $em->flush();
                    }

                }
            }
        }

        return new Response('OK');
    }

}
	