<?php

namespace App\Controller;

use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SerieRepository;
use App\Repository\EpisodeShowRepository;
use App\Repository\MovieRepository;
use Bugsnag\BugsnagBundle\DependencyInjection\ClientFactory;
use Bugsnag\Client;


class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @throws Exception
     */
    public function index(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository, MovieRepository $MovieRepository): Response
    {

        $series = $serieRepository->findAll();
        $episodes = $episodeShowRepository->findAll();
        $showSerie = [];

        foreach ($series as $serie) {
            if (count($serie->getEpisodeShows()->getValues()) > 0) {
                $showSerie[$serie->getName()] = $serie->getEpisodeShows()->getValues();
            }
        }

        $episodesByDate = [];
        $timeByDateType = [];
        $dateKeys = [];

        foreach ($episodes as $episode) {
            $dateKey = $episode->getShowDate()->format("Y-m-d");

            if (!isset($episodesByDate[$dateKey])) {
                $episodesByDate[$dateKey] = [$episode];
                $dateKeys[] = $dateKey;
            } else {
                $episodesByDate[$dateKey][] = $episode;
            }

            if (!isset($timeByDateType[$dateKey])) {
                $timeByDateType[$dateKey] = [
                    'Anime' => 0,
                    'Séries' => 0,
                    'Replay' => 0
                ];
            }

            $timeByDateType[$dateKey][$episode->getSerie()->getType()] += $episode->getDuration();
        }

        return $this->render('homepage/index.html.twig', [
            'series' => $showSerie,
            'episodes' => $episodes,
            'episodesByDate' => $episodesByDate,
            'dateKeys' => $dateKeys,
            'timeByDateType' => $timeByDateType
        ]);
    }
}
