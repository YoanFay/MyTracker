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
use App\Repository\EpisodeRepository;
use App\Repository\MovieRepository;
use App\Service\TimeService;
use Symfony\Component\Routing\Annotation\Route;


class HomepageController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/', name: 'home')]
    public function index(MovieRepository $movieRepository, EpisodeRepository $episodeRepository, MangaTomeRepository $mangaTomeRepository, GameRepository $gameRepository): Response
    {

        $now = new DateTime();
        $year = $now->format("Y");

        $globalTime = $episodeRepository->getDutation()['SUM'];
        $yearTime = $episodeRepository->getDutationByYear($year)['SUM'];

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
     * @throws Exception
     */
    #[Route('/homeInfo', name: 'home_info')]
    public function homeInfo(SerieUpdateRepository $serieUpdateRepository, TimeService $timeService, Request $request): Response
    {
        $count = $request->request->get('count', 0);

        $date = new DateTime($count.' days');
        $date->setTime(0, 0);

        $serieUpdate = $serieUpdateRepository->findBy(['updatedAt' => $date]);

        $updateByDate = [];

        foreach ($serieUpdate as $update) {

            $updateByDate[] = $update;
        }

        return $this->render("homepage/info.html.twig", [
            'next' => $count + 1,
            'previous' => $count - 1,
            'date' => $timeService->frenchFormatDate($date),
            'updateByDate' => $updateByDate,
        ]);
    }
}
