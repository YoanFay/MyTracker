<?php

namespace App\Controller;

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
     */
    public function index(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository): Response
    {
        
        $series = $serieRepository->findAll();
        $episodes = $episodeShowRepository->findAll();
        
        foreach($series as $serie){
            if(count($serie->getEpisodeShows()->getValues()) > 0){
                $showSerie[$serie->getName()] = $serie->getEpisodeShows()->getValues();
            }
        }

        $episodesByDate = [];
        $timeByDate = [];
        $dateKeys = [];

        foreach ($episodes as $episode) {
            $dateKey = date("Y-m-d", $episode->getShowDate());

            if (!isset($episodesByDate[$dateKey])) {
                $episodesByDate[$dateKey] = [$episode];
                $dateKeys[] = $dateKey;
            } else {
                $episodesByDate[$dateKey][] = $episode;
            }
        }

        return $this->render('homepage/index.html.twig', [
            'series' => $showSerie,
            'episodes' => $episodes,
            'episodesByDate' => $episodesByDate,
            'dateKeys' => $dateKeys,
        ]);
    }
}
