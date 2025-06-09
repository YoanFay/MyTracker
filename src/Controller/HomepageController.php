<?php

namespace App\Controller;

use App\Repository\EpisodeShowRepository;
use App\Repository\GameRepository;
use App\Repository\MangaTomeRepository;
use App\Repository\MovieShowRepository;
use App\Repository\SerieRepository;
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
    public function index(SerieRepository $serieRepository): Response
    {

        $releaseToday = $serieRepository->findBy(['nextAired' => new DateTime(), 'nextAiredType' => null]);

        return $this->render("homepage/index.html.twig", [
            'releaseToday' => $releaseToday
        ]);
    }


    /**
     * @throws Exception
     */
    #[Route('/homeInfo', name: 'home_info')]
    public function homeInfo(SerieUpdateRepository $serieUpdateRepository, TimeService $timeService, Request $request): Response
    {
        /** @var int $count */
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
