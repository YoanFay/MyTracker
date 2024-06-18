<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\MangaTomeRepository;
use App\Repository\SerieUpdateRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
            'countGameFullEndYear' => $countGameFullEndYear
        ]);
    }


    /**
     * @Route("/homeInfo", name="home_info")
     * @throws Exception
     */
    public function homeInfo(SerieUpdateRepository $serieUpdateRepository, TimeService $timeService, Request $request): Response
    {
        $count = $request->request->get('count', 0);

        dump($count - 1);

        $date = new DateTime($count.' days');
        $date->setTime(0, 0);
        dump($date);

        $serieUpdate = $serieUpdateRepository->findBy(['updatedAt' => $date]);

        $updateByDate = [];

        foreach ($serieUpdate as $update) {

            $newInfo = [
                'name' => $update->getSerie()->getName(),
                'next' => $update->getNewNextAired(),
                'status' => $update->getNewStatus()
            ];

            $updateByDate[] = $newInfo;
        }

        return $this->render("homepage/info.html.twig", [
            'next' => $count + 1,
            'previous' => $count - 1,
            'date' => $timeService->frenchFormatDate($date),
            'updateByDate' => $updateByDate,
        ]);
    }
}
