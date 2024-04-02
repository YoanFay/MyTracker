<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\MangaTomeRepository;
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
    public function index(MovieRepository $movieRepository, EpisodeShowRepository $episodeShowRepository, MangaTomeRepository $mangaTomeRepository, GameRepository $gameRepository): Response
    {

        $now = new DateTime();
        $year = $now->format("Y");

        $globalTime = $episodeShowRepository->getDutation()['SUM'];
        $yearTime = $episodeShowRepository->getDutationByYear($year)['SUM'];

        $globalTime += $movieRepository->getDuration()['SUM'];
        $yearTime += $movieRepository->getDutationByYear($year)['SUM'];

        $countReadingTome = $mangaTomeRepository->getTomeRead()['COUNT'];
        $countReadingTomeYear = $mangaTomeRepository->getTomeReadByYear($year)['COUNT'];

        $countGameEnd = $gameRepository->countGameEnd()['COUNT'];
        $countGameEndYear = $gameRepository->countGameEndByYear($year)['COUNT'];

        $countGameFullEnd = $gameRepository->countGameFullEnd()['COUNT'];
        $countGameFullEndYear = $gameRepository->countGameFullEndByYear($year)['COUNT'];

        return $this->render("homepage/index.html.twig", [
            'year' => $year,
            'globalTime' => $globalTime,
            'yearTime' => $yearTime,
            'countReadingTome' => $countReadingTome,
            'countReadingTomeYear' => $countReadingTomeYear,
            'countGameEnd' => $countGameEnd,
            'countGameEndYear' => $countGameEndYear,
            'countGameFullEnd' => $countGameFullEnd,
            'countGameFullEndYear' => $countGameFullEndYear,
        ]);
    }
}
