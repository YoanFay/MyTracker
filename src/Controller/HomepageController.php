<?php

namespace App\Controller;

use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Bugsnag\BugsnagBundle\DependencyInjection\ClientFactory;
use Bugsnag\Client;
use App\Repository\SerieRepository;
use App\Repository\EpisodeShowRepository;
use App\Repository\MovieRepository;
use App\Service\TimeService;


class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @throws Exception
     */
    public function index(MovieRepository $movieRepository, EpisodeShowRepository $episodeShowRepository, TimeService $timeService): Response
    {

        $movieDuration = $movieRepository->getDuration();
        $animeDuration = $episodeShowRepository->getDurationByType('Anime');
        $serieDuration = $episodeShowRepository->getDurationByType('Séries');
        $replayDuration = $episodeShowRepository->getDurationByType('Replay');
        
        $durationByGenre = $episodeShowRepository->getDurationGenre();
        $durationByTheme = $episodeShowRepository->getDurationTheme();

        $allEpisodes = $episodeShowRepository->findAll();
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

        foreach ($allEpisodes as $episode) {

            switch ($episode->getSerie()->getType()) {
            case "Anime":
                $animeByDay[$episode->getShowDate()->format('l')] += $episode->getDuration();
                break;
            case "Séries":
                $serieByDay[$episode->getShowDate()->format('l')] += $episode->getDuration();
                break;
            case "Replay":
                $replayByDay[$episode->getShowDate()->format('l')] += $episode->getDuration();
                break;
            }

        }

        foreach ($allMovies as $movie) {

            $movieByDay[$movie->getShowDate()->format('l')] += $movie->getDuration();

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

        $timeChart = "[".$animeDuration['COUNT'].", ".$serieDuration['COUNT'].", ".$replayDuration['COUNT'].", ".$movieDuration['COUNT']."]";

        $animeByDayChart = "[".$animeByDay['Monday'].", ".$animeByDay['Tuesday'].", ".$animeByDay['Wednesday'].", ".$animeByDay['Thursday'].", ".$animeByDay['Friday'].", ".$animeByDay['Saturday'].", ".$animeByDay['Sunday']."]";

        $serieByDayChart = "[".$serieByDay['Monday'].", ".$serieByDay['Tuesday'].", ".$serieByDay['Wednesday'].", ".$serieByDay['Thursday'].", ".$serieByDay['Friday'].", ".$serieByDay['Saturday'].", ".$serieByDay['Sunday']."]";

        $replayByDayChart = "[".$replayByDay['Monday'].", ".$replayByDay['Tuesday'].", ".$replayByDay['Wednesday'].", ".$replayByDay['Thursday'].", ".$replayByDay['Friday'].", ".$replayByDay['Saturday'].", ".$replayByDay['Sunday']."]";

        $movieByDayChart = "[".$movieByDay['Monday'].", ".$movieByDay['Tuesday'].", ".$movieByDay['Wednesday'].", ".$movieByDay['Thursday'].", ".$movieByDay['Friday'].", ".$movieByDay['Saturday'].", ".$movieByDay['Sunday']."]";
        
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

        return $this->render('homepage/index.html.twig', [
            'timeChart' => $timeChart,
            'animeByDayChart' => $animeByDayChart,
            'serieByDayChart' => $serieByDayChart,
            'replayByDayChart' => $replayByDayChart,
            'movieByDayChart' => $movieByDayChart,
            'labelGenreChart' => $labelGenreChart,
            'genreChart' => $genreChart,
            'labelThemeChart' => $labelThemeChart,
            'themeChart' => $themeChart,
        ]);
    }
}
