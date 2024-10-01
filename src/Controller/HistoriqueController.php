<?php

namespace App\Controller;

use App\Repository\EpisodeShowRepository;
use App\Repository\MovieShowRepository;
use App\Repository\SerieTypeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SerieRepository;
use App\Repository\EpisodeRepository;
use App\Repository\MovieRepository;
use Bugsnag\BugsnagBundle\DependencyInjection\ClientFactory;
use Bugsnag\Client;
use DateTime;

class HistoriqueController extends AbstractController
{
    #[Route('/historique', name: 'historique')]
    public function index(EpisodeShowRepository $episodeShowRepository, MovieShowRepository $movieShowRepository): Response
    {

        $listMonth = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre'
        ];

        $dates = $episodeShowRepository->findMonth();

        $listDate = [];

        foreach ($dates as $date) {

            $explode = explode('-', $date['DATE']);

            $year = $explode[0];
            $month = $listMonth[$explode[1]];
            $idMonth = $explode[1];

            if (!array_key_exists($year, $listDate)){

                $listDate[$year] = [
                    'Janvier' => [],
                    'Février' => [],
                    'Mars' => [],
                    'Avril' => [],
                    'Mai' => [],
                    'Juin' => [],
                    'Juillet' => [],
                    'Août' => [],
                    'Septembre' => [],
                    'Octobre' => [],
                    'Novembre' => [],
                    'Décembre' => [],
                    'TotalAnime' =>  0,
                    'TotalSéries' => 0,
                    'TotalReplay' => 0,
                    'TotalMovie' => 0,
                    'Total' => 0,
                ];
            }

            if ($listDate[$year][$month] === []) {
                $listDate[$year][$month] = [
                    'Anime' => 0,
                    'Séries' => 0,
                    'Replay' => 0,
                    'Movie' => 0,
                    'Total' => 0,
                    'ID' => $idMonth,
                ];
            }

            $listDate[$year][$month][$date['TYPE']] += $date['DURATION'];
            $listDate[$year][$month]['Total'] += $date['DURATION'];

            switch ($date['TYPE']) {
            case 'Anime':
                $listDate[$year]['TotalAnime'] += $date['DURATION'];
                break;
            case 'Séries':
                $listDate[$year]['TotalSéries'] += $date['DURATION'];
                break;
            case 'Replay':
                $listDate[$year]['TotalReplay'] += $date['DURATION'];
                break;
            }

            $listDate[$year]['Total'] += $date['DURATION'];

        }

        $dates = $movieShowRepository->findMonth();

        foreach ($dates as $date) {

            $explode = explode('-', $date['DATE']);

            $year = $explode[0];
            $month = $listMonth[$explode[1]];
            $idMonth = $explode[1];

            if (!array_key_exists($year, $listDate)){

                $listDate[$year] = [
                    'Janvier' => [],
                    'Février' => [],
                    'Mars' => [],
                    'Avril' => [],
                    'Mai' => [],
                    'Juin' => [],
                    'Juillet' => [],
                    'Août' => [],
                    'Septembre' => [],
                    'Octobre' => [],
                    'Novembre' => [],
                    'Décembre' => [],
                    'TotalAnime' =>  0,
                    'TotalSéries' => 0,
                    'TotalReplay' => 0,
                    'TotalMovie' => 0,
                    'Total' => 0,
                ];
            }

            if ($listDate[$year][$month] === []) {
                $listDate[$year][$month] = [
                    'Anime' => 0,
                    'Séries' => 0,
                    'Replay' => 0,
                    'Movie' => 0,
                    'Total' => 0,
                    'ID' => $idMonth,
                ];
            }

            $listDate[$year][$month]['Movie'] += $date['DURATION'];
            $listDate[$year][$month]['Total'] += $date['DURATION'];
            $listDate[$year]['TotalMovie'] += $date['DURATION'];
            $listDate[$year]['Total'] += $date['DURATION'];

        }

        return $this->render('historique\index.html.twig', [
            'list' => $listDate,
            'navLinkId' => 'historique',
        ]);

    }


    #[Route('/historique/all', name: 'historique_all')]
    public function allHistorique(EpisodeShowRepository $episodeShowRepository, MovieShowRepository $movieShowRepository): Response
    {

        $globalDuration = [
            'anime' => 0,
            'serie' => 0,
            'replay' => 0,
            'movie' => 0,
            'total' => 0,
        ];

        $dataByDate = [];

        $episodesShow = $episodeShowRepository->findAll();

        foreach ($episodesShow as $episodeShow) {

            $dateKey = $episodeShow->getShowDate()->format("Y-m-d");
            $episode = $episodeShow->getEpisode();
            $duration = $episode->getDuration();

            if(!array_key_exists($dateKey, $dataByDate)){
                $dataByDate[$dateKey] = [
                    'animeDuration' => 0,
                    'serieDuration' => 0,
                    'replayDuration' => 0,
                    'movieDuration' => 0,
                    'totalDuration' => 0,
                    'history' => []
                ];
            }

            $type = null;

            switch ($episode->getSerie()->getSerieType()->getName()) {
            case 'Anime':
                $dataByDate[$dateKey]['animeDuration'] += $duration;
                $globalDuration['anime'] += $duration;
                $type = 'Anime';
                break;
            case 'Séries':
                $dataByDate[$dateKey]['serieDuration'] += $duration;
                $globalDuration['serie'] += $duration;
                $type = 'Série';
                break;
            case 'Replay':
                $dataByDate[$dateKey]['replayDuration'] += $duration;
                $globalDuration['replay'] += $duration;
                $type = 'Replay';
                break;
            }

            $globalDuration['total'] += $duration;
            $dataByDate[$dateKey]['totalDuration'] += $duration;

            $episodeNumber = $episode->getEpisodeNumber();

            if ($episodeNumber < 10){
                $episodeNumber = "0".$episodeNumber;
            }

            $saisonNumber = $episode->getSaisonNumber();

            if ($saisonNumber < 10){
                $saisonNumber = "0".$saisonNumber;
            }

            $name = $episode->getSerie()->getName()." - S".$saisonNumber."E".$episodeNumber." : ".$episode->getName();

            $dataByDate[$dateKey]['history'][] = [
                'id' => $episode->getSerie()->getId(),
                'name' => $name,
                'show' => $episodeShow->getShowDate(),
                'duration' => $duration,
                'type' => $type,
            ];
        }

        $moviesShow = $movieShowRepository->findAll();

        foreach ($moviesShow as $movieShow) {

            $dateKey = $movieShow->getShowDate()->format("Y-m-d");
            $movie = $movieShow->getMovie();
            $duration = $movie->getDuration();

            if(!array_key_exists($dateKey, $dataByDate)){
                $dataByDate[$dateKey] = [
                    'animeDuration' => 0,
                    'serieDuration' => 0,
                    'replayDuration' => 0,
                    'movieDuration' => 0,
                    'totalDuration' => 0,
                    'history' => []
                ];
            }

            $dataByDate[$dateKey]['movieDuration'] += $duration;
            $globalDuration['movie'] += $duration;
            $globalDuration['total'] += $duration;
            $dataByDate[$dateKey]['totalDuration'] += $duration;

            $dataByDate[$dateKey]['history'][] = [
                'id' => $movie->getId(),
                'name' => $movie->getName(),
                'show' => $movieShow->getShowDate(),
                'duration' => $duration,
                'type' => 'Movie'
            ];

        }

        $currentDate = new DateTime('now');
        $startOfYear = new DateTime($currentDate->format('Y') . '-01-01');
        $daysSinceStartOfYear = $currentDate->diff($startOfYear)->days + 1;

        foreach ($globalDuration as $key => $duration){

            $globalDuration[$key] = $duration / $daysSinceStartOfYear;

        }

        krsort($dataByDate);

        return $this->render('historique/allHistorique.html.twig', [
            'dataByDate' => $dataByDate,
            'globalDuration' => $globalDuration,
            'navLinkId' => 'episode',
        ]);
    }


    #[Route('/historique/categorie/{categorie}', name: 'historique_categories')]
    public function historiqueCategories(EpisodeShowRepository $episodeShowRepository, SerieTypeRepository $serieTypeRepository, $categorie): Response
    {

        $voyelle = array('a', 'e', 'i', 'o', 'u');

        $serieType = $serieTypeRepository->find($categorie);

        $title = $serieType->getName();
        $nav = strtolower($title);

        if (in_array($title[0], $voyelle)) {
            $text = "d'".$nav;
        } else {
            $text = "de ".$nav;
        }

        $episodesShow = $episodeShowRepository->findBySerieType($serieType);

        $episodesByDate = [];
        $dateKeys = [];
        $globalDuration = 0;

        foreach ($episodesShow as $episodeShow) {

            $episode = $episodeShow->getEpisode();

            $dateKey = $episodeShow->getShowDate()->format("Y-m-d");

            $globalDuration += $episode->getDuration();

            if (!isset($episodesByDate[$dateKey])) {
                $episodesByDate[$dateKey] = [$episodeShow];
                $dateKeys[] = $dateKey;
            } else {
                $episodesByDate[$dateKey][] = $episodeShow;
            }
        }

        return $this->render('historique/categories.html.twig', [
            'episodes' => $episodesShow,
            'episodesByDate' => $episodesByDate,
            'dateKeys' => $dateKeys,
            'globalDuration' => $globalDuration,
            'title' => $title,
            'text' => $text,
            'navLinkId' => 'episode_'.$nav.'_list',
        ]);
    }


    #[Route('/historique/date/{year}/{month}', name: 'historique_date')]
    public function historiqueDate(EpisodeShowRepository $episodeShowRepository, SerieRepository $serieRepository, $year = 0, $month = 0): Response
    {

        $currentDate = new DateTime();
        $testCurrent = false;

        if ($year === 0) {
            $year = '%';
        }

        if ($month === 0) {
            $month = '%';
            $startDate = new DateTime($year.'-01-01');
            $endDate = new DateTime($year.'-12-31');
            $testCurrent = true;
        } else {
            $startDate = new DateTime($year.'-'.$month.'-01');
            $endDate = new DateTime($startDate->format($year.'-'.$month.'-t'));
        }

        if ($endDate > $currentDate && ($endDate->format('m') === $currentDate->format('m') || $testCurrent)) {
            $endDate = $currentDate;
        }

        $daysSinceStartOfYear = $startDate->diff($endDate)->days + 1;

        $series = $serieRepository->findAll();
        $episodesShow = $episodeShowRepository->findByDate($year, $month);
        $showSerie = [];

        $listMonth = [
            '%' => '',
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre'
        ];

        $month = $listMonth[$month];

        foreach ($series as $serie) {
            if (count($serie->getEpisodes()->getValues()) > 0) {
                $showSerie[$serie->getName()] = $serie->getEpisodes()->getValues();
            }
        }

        $episodesByDate = [];
        $timeByDateType = [];
        $dateKeys = [];
        $globalDuration = 0;
        $globalDurationAnime = 0;
        $globalDurationSerie = 0;
        $globalDurationReplay = 0;

        foreach ($episodesShow as $episodeShow) {

            $episode = $episodeShow->getEpisode();

            $dateKey = $episodeShow->getShowDate()->format("Y-m-d");

            $globalDuration += $episode->getDuration();

            switch ($episode->getSerie()->getSerieType()->getName()) {
            case 'Anime':
                $globalDurationAnime += $episode->getDuration();
                break;
            case 'Séries':
                $globalDurationSerie += $episode->getDuration();
                break;
            case 'Replay':
                $globalDurationReplay += $episode->getDuration();
                break;

            }

            if (!isset($episodesByDate[$dateKey])) {
                $episodesByDate[$dateKey] = [$episodeShow];
                $dateKeys[] = $dateKey;
            } else {
                $episodesByDate[$dateKey][] = $episodeShow;
            }

            if (!isset($timeByDateType[$dateKey])) {
                $timeByDateType[$dateKey] = [
                    'Anime' => 0,
                    'Séries' => 0,
                    'Replay' => 0
                ];
            }

            $timeByDateType[$dateKey][$episode->getSerie()->getSerieType()->getName()] += $episode->getDuration();
        }

        return $this->render('historique/historiqueDate.html.twig', [
            'year' => $year,
            'month' => $month,
            'series' => $showSerie,
            'episodes' => $episodeShow,
            'episodesByDate' => $episodesByDate,
            'dateKeys' => $dateKeys,
            'timeByDateType' => $timeByDateType,
            'globalDuration' => $globalDuration,
            'globalDurationAnime' => $globalDurationAnime,
            'globalDurationSerie' => $globalDurationSerie,
            'globalDurationReplay' => $globalDurationReplay,
            'daysSinceStartOfYear' => $daysSinceStartOfYear,
            'navLinkId' => 'episode',
        ]);
    }
}
                                    