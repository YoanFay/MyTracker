<?php

namespace App\Controller;

use App\Repository\EpisodeRepository;
use App\Repository\EpisodeShowRepository;
use App\Repository\MangaRepository;
use App\Repository\MovieRepository;
use App\Service\TimeService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    
    /**
    * @Route("/stat/episode", name="episode_stat")
    * @throws Exception
    */
    public function episodeStat(MovieRepository $movieRepository, EpisodeRepository $episodeRepository, TimeService $timeService, EpisodeShowRepository $episodeShowRepository): Response
    {
        
        $movieDuration = $movieRepository->getDuration();
        $animeDuration = $episodeRepository->getDurationByType('Anime');
        $serieDuration = $episodeRepository->getDurationByType('Séries');
        $replayDuration = $episodeRepository->getDurationByType('Replay');

        $durations = $episodeRepository->getDurationByType();

        foreach ($durations as $duration){
            switch ($duration['TYPE']){
            case 'Anime':
                $animeDuration = $duration['COUNT'];
                break;
            case 'Séries':
                $serieDuration = $duration['COUNT'];
                break;
            case 'Replay':
                $replayDuration = $duration['COUNT'];
                break;
            }
        }
        
        $durationByGenre = $episodeRepository->getDurationGenre();
        $durationByTheme = $episodeRepository->getDurationTheme();
        
        $allEpisodesShow = $episodeShowRepository->findAll();
        $allMovies = $movieRepository->findAll();
        
        $animeByDay = [
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
            'Sunday' => 0
        ];
        
        $serieByDay = [
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
            'Sunday' => 0
        ];
        
        $replayByDay = [
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
            'Sunday' => 0
        ];
        
        $movieByDay = [
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
            'Sunday' => 0
        ];
        
        $animeByMonth = [
            'January' => 0,
            'February' => 0,
            'March' => 0,
            'April' => 0,
            'May' => 0,
            'June' => 0,
            'July' => 0,
            'August' => 0,
            'September' => 0,
            'October' => 0,
            'November' => 0,
            'December' => 0
        ];
        
        $serieByMonth = [
            'January' => 0,
            'February' => 0,
            'March' => 0,
            'April' => 0,
            'May' => 0,
            'June' => 0,
            'July' => 0,
            'August' => 0,
            'September' => 0,
            'October' => 0,
            'November' => 0,
            'December' => 0
        ];
        
        $replayByMonth = [
            'January' => 0,
            'February' => 0,
            'March' => 0,
            'April' => 0,
            'May' => 0,
            'June' => 0,
            'July' => 0,
            'August' => 0,
            'September' => 0,
            'October' => 0,
            'November' => 0,
            'December' => 0
        ];
        
        $movieByMonth = [
            'January' => 0,
            'February' => 0,
            'March' => 0,
            'April' => 0,
            'May' => 0,
            'June' => 0,
            'July' => 0,
            'August' => 0,
            'September' => 0,
            'October' => 0,
            'November' => 0,
            'December' => 0
        ];
        
        
        foreach ($allEpisodesShow as $episodeShow) {

            $episode = $episodeShow->getEpisode();
            
            switch ($episode->getSerie()->getSerieType()->getName()) {
                case "Anime":
                    $animeByDay[$episodeShow->getShowDate()->format('l')] += $episode->getDuration();
                    $animeByMonth[$episodeShow->getShowDate()->format('F')] += $episode->getDuration();
                    break;
                    case "Séries":
                        $serieByDay[$episodeShow->getShowDate()->format('l')] += $episode->getDuration();
                        $serieByMonth[$episodeShow->getShowDate()->format('F')] += $episode->getDuration();
                        break;
                        case "Replay":
                            $replayByDay[$episodeShow->getShowDate()->format('l')] += $episode->getDuration();
                            $replayByMonth[$episodeShow->getShowDate()->format('F')] += $episode->getDuration();
                            break;
                        }
                        
                    }
                    
                    foreach ($allMovies as $movie) {
                        
                        $movieByDay[$movie->getShowDate()->format('l')] += $movie->getDuration();
                        $movieByMonth[$movie->getShowDate()->format('F')] += $movie->getDuration();
                        
                    }
                    
                    $aujourdHui = new DateTime();
                    
                    $debutAnnee = new DateTime($aujourdHui->format('Y-01-01'));
                    
                    $joursCount = [
                        'Monday' => 0,
                        'Tuesday' => 0,
                        'Wednesday' => 0,
                        'Thursday' => 0,
                        'Friday' => 0,
                        'Saturday' => 0,
                        'Sunday' => 0
                    ];
                    
                    // Boucler à travers chaque jour depuis le début de l'année
                    while ($debutAnnee <= $aujourdHui) {
                        $jourSemaine = $debutAnnee->format('l');
                        $joursCount[$jourSemaine]++;
                        $debutAnnee->modify('+1 day');
                    }
                    
                    foreach ($animeByDay as $key => $time) {
                        $animeByDay[$key] = $time / $joursCount[$key];
                    }
                    
                    foreach ($serieByDay as $key => $time) {
                        $serieByDay[$key] = $time / $joursCount[$key];
                    }
                    
                    foreach ($replayByDay as $key => $time) {
                        $replayByDay[$key] = $time / $joursCount[$key];
                    }
                    
                    foreach ($movieByDay as $key => $time) {
                        $movieByDay[$key] = $time / $joursCount[$key];
                    }
                    
                    $timeChart = "[".$animeDuration.", ".$serieDuration.", ".$replayDuration.", ".$movieDuration['SUM']."]";
                    
                    $animeByDayChart = "[".$animeByDay['Monday'].", ".$animeByDay['Tuesday'].", ".$animeByDay['Wednesday'].", ".$animeByDay['Thursday'].", ".$animeByDay['Friday'].", ".$animeByDay['Saturday'].", ".$animeByDay['Sunday']."]";
                    
                    $serieByDayChart = "[".$serieByDay['Monday'].", ".$serieByDay['Tuesday'].", ".$serieByDay['Wednesday'].", ".$serieByDay['Thursday'].", ".$serieByDay['Friday'].", ".$serieByDay['Saturday'].", ".$serieByDay['Sunday']."]";
                    
                    $replayByDayChart = "[".$replayByDay['Monday'].", ".$replayByDay['Tuesday'].", ".$replayByDay['Wednesday'].", ".$replayByDay['Thursday'].", ".$replayByDay['Friday'].", ".$replayByDay['Saturday'].", ".$replayByDay['Sunday']."]";
                    
                    $movieByDayChart = "[".$movieByDay['Monday'].", ".$movieByDay['Tuesday'].", ".$movieByDay['Wednesday'].", ".$movieByDay['Thursday'].", ".$movieByDay['Friday'].", ".$movieByDay['Saturday'].", ".$movieByDay['Sunday']."]";
                    
                    $animeByMonthChart = "[".$animeByMonth['January'].", ".$animeByMonth['February'].", ".$animeByMonth['March'].", ".$animeByMonth['April'].", ".$animeByMonth['May'].", ".$animeByMonth['June'].", ".$animeByMonth['July'].", ".$animeByMonth['August'].", ".$animeByMonth['September'].", ".$animeByMonth['October'].", ".$animeByMonth['November'].", ".$animeByMonth['December']."]";
                    
                    $serieByMonthChart = "[".$serieByMonth['January'].", ".$serieByMonth['February'].", ".$serieByMonth['March'].", ".$serieByMonth['April'].", ".$serieByMonth['May'].", ".$serieByMonth['June'].", ".$serieByMonth['July'].", ".$serieByMonth['August'].", ".$serieByMonth['September'].", ".$serieByMonth['October'].", ".$serieByMonth['November'].", ".$serieByMonth['December']."]";
                    
                    $replayByMonthChart = "[".$replayByMonth['January'].", ".$replayByMonth['February'].", ".$replayByMonth['March'].", ".$replayByMonth['April'].", ".$replayByMonth['May'].", ".$replayByMonth['June'].", ".$replayByMonth['July'].", ".$replayByMonth['August'].", ".$replayByMonth['September'].", ".$replayByMonth['October'].", ".$replayByMonth['November'].", ".$replayByMonth['December']."]";
                    
                    $movieByMonthChart = "[".$movieByMonth['January'].", ".$movieByMonth['February'].", ".$movieByMonth['March'].", ".$movieByMonth['April'].", ".$movieByMonth['May'].", ".$movieByMonth['June'].", ".$movieByMonth['July'].", ".$movieByMonth['August'].", ".$movieByMonth['September'].", ".$movieByMonth['October'].", ".$movieByMonth['November'].", ".$movieByMonth['December']."]";
                    
                    
                    $labelGenreChart = "[";
                    $genreChart = "[";
                    
                    foreach ($durationByGenre as $duration) {
                        
                        //if($duration['name'] !== "Hentai"){
                            $labelGenreChart .= '"'.$duration['name'] . '", ';
                            $genreChart .= $duration['COUNT'] . ", ";
                            //}
                        }
                        
                        $labelGenreChart = rtrim($labelGenreChart, ", ") . "]";
                        $genreChart = rtrim($genreChart, ", ") . "]";
                        
                        $labelThemeChart = "[";
                        $themeChart = "[";
                        
                        foreach ($durationByTheme as $duration) {
                            
                            //if($duration['name'] !== "Sexe"){
                                $labelThemeChart .= '"'.$duration['name'] . '", ';
                                $themeChart .= $duration['COUNT'] . ", ";
                                //}
                            }
                            
                            
                            $labelThemeChart = rtrim($labelThemeChart, ", ") . "]";
                            $themeChart = rtrim($themeChart, ", ") . "]";
                            
                            return $this->render('stats/episode.html.twig', [
                                'timeChart' => $timeChart,
                                'animeByDayChart' => $animeByDayChart,
                                'serieByDayChart' => $serieByDayChart,
                                'replayByDayChart' => $replayByDayChart,
                                'movieByDayChart' => $movieByDayChart,
                                'animeByMonthChart' => $animeByMonthChart,
                                'serieByMonthChart' => $serieByMonthChart,
                                'replayByMonthChart' => $replayByMonthChart,
                                'movieByMonthChart' => $movieByMonthChart,
                                'labelGenreChart' => $labelGenreChart,
                                'genreChart' => $genreChart,
                                'labelThemeChart' => $labelThemeChart,
                                'themeChart' => $themeChart,
                                'navLinkId' => 'homepage',
                            ]);
                        }
                        
                        #[Route('/stat/manga', name: 'manga_stat')]
                        public function mangaStat(MangaRepository $mangaRepository): Response
                        {
                            
                            $mangaTomesByGenre = $mangaRepository->getMangaTomeByGenre();
                            $mangaTomesByTheme = $mangaRepository->getMangaTomeByTheme();
                            
                            $labelGenreChart = "[";
                            $genreChart = "[";
                            
                            foreach ($mangaTomesByGenre as $count) {
                                
                                $labelGenreChart .= '"'.$count['name'] . '", ';
                                $genreChart .= $count['COUNT'] . ", ";
                            }
                            
                            $labelGenreChart = rtrim($labelGenreChart, ", ") . "]";
                            $genreChart = rtrim($genreChart, ", ") . "]";
                            
                            $labelThemeChart = "[";
                            $themeChart = "[";
                            
                            foreach ($mangaTomesByTheme as $count) {
                                
                                $labelThemeChart .= '"'.$count['name'] . '", ';
                                $themeChart .= $count['COUNT'] . ", ";
                            }
                            
                            
                            $labelThemeChart = rtrim($labelThemeChart, ", ") . "]";
                            $themeChart = rtrim($themeChart, ", ") . "]";
                            
                            return $this->render('stats/manga.html.twig', [
                                'labelGenreChart' => $labelGenreChart,
                                'genreChart' => $genreChart,
                                'labelThemeChart' => $labelThemeChart,
                                'themeChart' => $themeChart,
                                'navLinkId' => 'manga_stat',
                            ]);
                        }
                        
                        #[Route('/stat/anime', name: 'anime_stat')]
                        public function animeStat(MovieRepository $movieRepository, EpisodeRepository $episodeRepository, TimeService $timeService): Response
                        {
                            
                            $durationByGenre = $episodeRepository->getDurationGenre();
                            $durationByTheme = $episodeRepository->getDurationTheme();
                            
                            $labelGenreChart = "[";
                            $genreChart = "[";
                            
                            foreach ($durationByGenre as $duration) {
                                
                                $labelGenreChart .= '"'.$duration['name'] . '", ';
                                $genreChart .= $duration['COUNT'] . ", ";
                            }
                            
                            $labelGenreChart = rtrim($labelGenreChart, ", ") . "]";
                            $genreChart = rtrim($genreChart, ", ") . "]";
                            
                            $labelThemeChart = "[";
                            $themeChart = "[";
                            
                            foreach ($durationByTheme as $duration) {
                                
                                $labelThemeChart .= '"'.$duration['name'] . '", ';
                                $themeChart .= $duration['COUNT'] . ", ";
                            }
                            
                            
                            $labelThemeChart = rtrim($labelThemeChart, ", ") . "]";
                            $themeChart = rtrim($themeChart, ", ") . "]";
                            
                            return $this->render('stats/anime.html.twig', [
                                'labelGenreChart' => $labelGenreChart,
                                'genreChart' => $genreChart,
                                'labelThemeChart' => $labelThemeChart,
                                'themeChart' => $themeChart,
                                'navLinkId' => 'anime_stat',
                            ]);
                        }
                    }
                    