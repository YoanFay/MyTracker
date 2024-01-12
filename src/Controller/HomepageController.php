<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SerieRepository;
use App\Repository\EpisodeShowRepository;
use Bugsnag\BugsnagBundle\DependencyInjection\ClientFactory;
use Bugsnag\Client;


class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @throws \Exception
     */
    public function index(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository): Response
    {
        
        $series = $serieRepository->findAll();
        $episodes = $episodeShowRepository->findAll();
        $showSerie = [];
        
        foreach($series as $serie){
            if(count($serie->getEpisodeShows()->getValues()) > 0){
                $showSerie[$serie->getName()] = $serie->getEpisodeShows()->getValues();
            }
        }

        $episodesByDate = [];
        $timeByDate = [];
        $dateKeys = [];

        foreach ($episodes as $episode) {
            $dateKey = $episode->getShowDate()->format("Y-m-d");

            if (!isset($episodesByDate[$dateKey])) {
                $episodesByDate[$dateKey] = [$episode];
                $dateKeys[] = $dateKey;
            } else {
                $episodesByDate[$dateKey][] = $episode;
            }
        }


        $globalDuration = 0;
        $countDay = 0;

        asort($dateKeys);
        $sortedDateKeys = array_keys($dateKeys);
        $duration = [];

        foreach ($sortedDateKeys as $dateKey) {
            $date = (new DateTime($dateKey))->format('d/m/Y');
            $dateShow = (new DateTime($dateKey))->format('Y/m/d');
            $duration = [];

            foreach ($episodesByDate as $key => $episodes) {
                $currentDuration = 0;

                foreach ($episodes as $episode) {
                    $globalDuration += $episode['duration'];
                    $currentDuration += $episode['duration'];
                }

                $countDay++;

                $duration[$key] = $currentDuration;
            }
        }

        $averageDuration = 0;

        if ($countDay !== 0){
            $averageDuration = ($globalDuration / $countDay);
        }

        return $this->render('homepage/index.html.twig', [
            'series' => $showSerie,
            'episodes' => $episodes,
            'episodesByDate' => $episodesByDate,
            'dateKeys' => $dateKeys,
            'duration' => $duration,
            'averageDuration' => $averageDuration
        ]);
    }
}
