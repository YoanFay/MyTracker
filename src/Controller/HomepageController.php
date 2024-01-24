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
        
        $allEpisodes = $episodeShowRepository->findAll();
        
        $timeByDay = [
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
            'Sunday' => 0
        ];

        
        foreach($allEpisodes as $episode){
            $timeByDay[$episode->getShowDate()->format('l')] += $episode->getDuration();
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
        
        foreach($timeByDay as $key => $time){
            $timeByDay[$key] = $time / $joursCount[$key];
        }
        
        $animeType = $timeService->convertirMillisecondes($animeDuration['COUNT']);
        
        $timeChart = "[".$movieDuration['COUNT'].", ".$animeDuration['COUNT'].", ".$serieDuration['COUNT'].", ".$replayDuration['COUNT']."]";
        
        $timeByDayChart = "[".$timeByDay['Monday'].", ".$timeByDay['Tuesday'].", ".$timeByDay['Wednesday'].", ".$timeByDay['Thursday'].", ".$timeByDay['Friday'].", ".$timeByDay['Saturday'].", ".$timeByDay['Sunday']."]";
        
        return $this->render('homepage/index.html.twig', [
            'timeChart' => $timeChart,
            'timeByDayChart' => $timeByDayChart,
        ]);
    }
}
