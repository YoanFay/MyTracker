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
        
        $animeType = $timeService->convertirMillisecondes($animeDuration['COUNT']);
        
        $timeChart = "[".$movieDuration['COUNT'].", ".$animeDuration['COUNT'].", ".$serieDuration['COUNT'].", ".$replayDuration['COUNT']."]";
        
        dump($timeChart);
        
        return $this->render('homepage/index.html.twig', [
            'timeChart' => $timeChart,
        ]);
    }
}
