<?php

namespace App\Controller;

use App\Repository\EpisodeShowRepository;
use App\Repository\MovieShowRepository;
use App\Repository\SerieTypeRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

#[Route('/historique')]
class HistoriqueController extends AbstractController
{
    #[Route('/', name: 'historique')]
    public function index(EpisodeShowRepository $episodeShowRepository, MovieShowRepository $movieShowRepository): Response
    {

        $listMonth = [
            '01' => 'janvier',
            '02' => 'fevrier',
            '03' => 'mars',
            '04' => 'avril',
            '05' => 'mai',
            '06' => 'juin',
            '07' => 'juillet',
            '08' => 'aout',
            '09' => 'septembre',
            '10' => 'octobre',
            '11' => 'novembre',
            '12' => 'decembre'
        ];

        $dates = $episodeShowRepository->findMonth();

        $listDate = [];

        foreach ($dates as $date) {

            $explode = explode('-', $date['DATE']);

            $year = $explode[0];
            $month = $listMonth[$explode[1]];
            $idMonth = $explode[1];

            if (!array_key_exists($year, $listDate)) {

                $listDate[$year] = [
                    'janvier' => [],
                    'fevrier' => [],
                    'mars' => [],
                    'avril' => [],
                    'mai' => [],
                    'juin' => [],
                    'juillet' => [],
                    'aout' => [],
                    'septembre' => [],
                    'octobre' => [],
                    'novembre' => [],
                    'decembre' => [],
                    'totalAnime' => 0,
                    'totalSeries' => 0,
                    'totalReplay' => 0,
                    'totalMovie' => 0,
                    'total' => 0,
                ];
            }

            if ($listDate[$year][$month] === []) {
                $listDate[$year][$month] = [
                    'anime' => 0,
                    'series' => 0,
                    'replay' => 0,
                    'movie' => 0,
                    'total' => 0,
                    'id' => $idMonth,
                ];
            }

            $listDate[$year][$month]['total'] += $date['DURATION'];

            switch ($date['TYPE']) {
            case 'Anime':
                $listDate[$year][$month]['anime'] += $date['DURATION'];
                $listDate[$year]['totalAnime'] += $date['DURATION'];
                break;
            case 'Séries':
                $listDate[$year][$month]['series'] += $date['DURATION'];
                $listDate[$year]['totalSeries'] += $date['DURATION'];
                break;
            case 'Replay':
                $listDate[$year][$month]['replay'] += $date['DURATION'];
                $listDate[$year]['totalReplay'] += $date['DURATION'];
                break;
            }

            $listDate[$year]['total'] += $date['DURATION'];

        }

        $dates = $movieShowRepository->findMonth();

        foreach ($dates as $date) {

            $explode = explode('-', $date['DATE']);

            $year = $explode[0];
            $month = $listMonth[$explode[1]];
            $idMonth = $explode[1];

            if (!array_key_exists($year, $listDate)) {

                $listDate[$year] = [
                    'janvier' => [],
                    'fevrier' => [],
                    'mars' => [],
                    'avril' => [],
                    'mai' => [],
                    'juin' => [],
                    'juillet' => [],
                    'aout' => [],
                    'septembre' => [],
                    'octobre' => [],
                    'novembre' => [],
                    'decembre' => [],
                    'totalAnime' => 0,
                    'totalSeries' => 0,
                    'totalReplay' => 0,
                    'totalMovie' => 0,
                    'total' => 0,
                ];
            }

            if ($listDate[$year][$month] === []) {
                $listDate[$year][$month] = [
                    'anime' => 0,
                    'series' => 0,
                    'replay' => 0,
                    'movie' => 0,
                    'total' => 0,
                    'id' => $idMonth,
                ];
            }

            $listDate[$year][$month]['movie'] += $date['DURATION'];
            $listDate[$year][$month]['total'] += $date['DURATION'];
            $listDate[$year]['totalMovie'] += $date['DURATION'];
            $listDate[$year]['total'] += $date['DURATION'];

        }

        krsort($listDate);

        return $this->render('historique\index.html.twig', [
            'list' => $listDate,
            'navLinkId' => 'historique',
        ]);

    }


    /**
     * @throws Exception
     */
    #[Route('/all', name: 'historique_all')]
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

            if (!array_key_exists($dateKey, $dataByDate)) {
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

            if ($episodeNumber < 10) {
                $episodeNumber = "0".$episodeNumber;
            }

            $saisonNumber = $episode->getSaisonNumber();

            if ($saisonNumber < 10) {
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

            if (!array_key_exists($dateKey, $dataByDate)) {
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

        krsort($dataByDate);

        $startDate = new DateTime(array_key_last($dataByDate));
        $endDate = new DateTime('now');
        $daysSinceStartOfYear = $endDate->diff($startDate)->days + 1;

        foreach ($globalDuration as $key => $duration) {

            $globalDuration[$key] = $duration / $daysSinceStartOfYear;

        }

        return $this->render('historique/allHistorique.html.twig', [
            'dataByDate' => $dataByDate,
            'globalDuration' => $globalDuration,
            'navLinkId' => 'episode',
        ]);
    }


    /**
     * @throws Exception
     */
    #[Route('/categorie/{categorie}', name: 'historique_categories')]
    public function historiqueCategories(EpisodeShowRepository $episodeShowRepository, SerieTypeRepository $serieTypeRepository, MovieShowRepository $movieShowRepository, $categorie): Response
    {

        if ($categorie !== 'movie') {

            $voyelle = array('a', 'e', 'i', 'o', 'u');

            $serieType = $serieTypeRepository->findOneBy(['slug' => $categorie]);

            $title = $serieType->getName();
            $nav = strtolower($title);

            if (in_array($title[0], $voyelle)) {
                $text = "d'".$nav;
            } else {
                $text = "de ".$nav;
            }

            $episodesShow = $episodeShowRepository->findBySerieType($serieType);

            $dataByDate = [];
            $globalDuration = 0;

            foreach ($episodesShow as $episodeShow) {

                $dateKey = $episodeShow->getShowDate()->format("Y-m-d");

                if (!array_key_exists($dateKey, $dataByDate)) {
                    $dataByDate[$dateKey] = [
                        'totalDuration' => 0,
                        'history' => [],
                    ];
                }

                $episode = $episodeShow->getEpisode();
                $duration = $episode->getDuration();

                $globalDuration += $duration;

                $episodeNumber = $episode->getEpisodeNumber();

                if ($episodeNumber < 10) {
                    $episodeNumber = "0".$episodeNumber;
                }

                $saisonNumber = $episode->getSaisonNumber();

                if ($saisonNumber < 10) {
                    $saisonNumber = "0".$saisonNumber;
                }

                $name = $episode->getSerie()->getName()." - S".$saisonNumber."E".$episodeNumber." : ".$episode->getName();

                $dataByDate[$dateKey]['history'][] = [
                    'id' => $episode->getSerie()->getId(),
                    'name' => $name,
                    'show' => $episodeShow->getShowDate(),
                    'duration' => $duration,
                ];
                $dataByDate[$dateKey]['totalDuration'] += $duration;
            }

        } else {

            $text = "de films";
            $title = "Films";

            $moviesShow = $movieShowRepository->findAll();

            $dataByDate = [];
            $globalDuration = 0;

            foreach ($moviesShow as $movieShow) {

                $dateKey = $movieShow->getShowDate()->format("Y-m-d");

                if (!array_key_exists($dateKey, $dataByDate)) {
                    $dataByDate[$dateKey] = [
                        'totalDuration' => 0,
                        'history' => [],
                    ];
                }

                $movie = $movieShow->getMovie();
                $duration = $movie->getDuration();

                $globalDuration += $duration;

                $dataByDate[$dateKey]['history'][] = [
                    'id' => $movie->getId(),
                    'name' => $movie->getName(),
                    'show' => $movieShow->getShowDate(),
                    'duration' => $duration,
                ];
                $dataByDate[$dateKey]['totalDuration'] += $duration;

            }
        }

        krsort($dataByDate);

        $startDate = new DateTime(array_key_last($dataByDate));
        $endDate = new DateTime('now');
        $daysSinceStartOfYear = $endDate->diff($startDate)->days + 1;

        $globalDurationAverage = $globalDuration / $daysSinceStartOfYear;

        return $this->render('historique/categories.html.twig', [
            'dataByDate' => $dataByDate,
            'globalDuration' => $globalDuration,
            'globalDurationAverage' => $globalDurationAverage,
            'title' => $title,
            'categorie' => $categorie,
            'text' => $text,
            'navLinkId' => 'history_'.$categorie.'_list',
        ]);
    }


    /**
     * @throws Exception
     */
    #[Route('/{year}/{month}', name: 'historique_date')]
    public function historiqueDate(EpisodeShowRepository $episodeShowRepository, MovieShowRepository $movieShowRepository, $year = 0, $month = 0): Response
    {

        $globalDuration = [
            'anime' => 0,
            'serie' => 0,
            'replay' => 0,
            'movie' => 0,
            'total' => 0,
        ];

        $dataByDate = [];

        $episodesShow = $episodeShowRepository->findByDate($year, $month);

        foreach ($episodesShow as $episodeShow) {

            $dateKey = $episodeShow->getShowDate()->format("Y-m-d");
            $episode = $episodeShow->getEpisode();
            $duration = $episode->getDuration();

            if (!array_key_exists($dateKey, $dataByDate)) {
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

            if ($episodeNumber < 10) {
                $episodeNumber = "0".$episodeNumber;
            }

            $saisonNumber = $episode->getSaisonNumber();

            if ($saisonNumber < 10) {
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

        $moviesShow = $movieShowRepository->findByDate($year, $month);

        foreach ($moviesShow as $movieShow) {

            $dateKey = $movieShow->getShowDate()->format("Y-m-d");
            $movie = $movieShow->getMovie();
            $duration = $movie->getDuration();

            if (!array_key_exists($dateKey, $dataByDate)) {
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

        $startDate = new DateTime($year.'-'.$month.'-01');

        $endDate = new DateTime('now');

        if (!($month === $endDate->format('m') && $year === $endDate->format('Y'))) {
            $endDate->setDate($year, $month, '1');
            $endDate = $endDate->modify('last day of this month');
            $endDate->setTime(23, 59, 59, 999999);
        }
        $daysSinceStartOfYear = $endDate->diff($startDate)->days + 1;

        foreach ($globalDuration as $key => $duration) {

            $globalDuration[$key] = $duration / $daysSinceStartOfYear;

        }

        krsort($dataByDate);

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

        return $this->render('historique/historiqueDate.html.twig', [
            'year' => $year,
            'month' => $listMonth[$month],
            'dataByDate' => $dataByDate,
            'globalDuration' => $globalDuration,
            'daysSinceStartOfYear' => $daysSinceStartOfYear,
            'navLinkId' => 'episode',
        ]);
    }
}
                                    