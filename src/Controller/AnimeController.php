<?php

namespace App\Controller;

use App\Repository\EpisodeShowRepository;
use App\Repository\MovieRepository;
use App\Service\TimeService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnimeController extends AbstractController
{

    #[Route('/anime/statistique', name: 'anime_stat')]
    public function animeStat(MovieRepository $movieRepository, EpisodeShowRepository $episodeShowRepository, TimeService $timeService): Response
    {

        $durationByGenre = $episodeShowRepository->getDurationGenre();
        $durationByTheme = $episodeShowRepository->getDurationTheme();

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

        return $this->render('anime/stat.html.twig', [
            'labelGenreChart' => $labelGenreChart,
            'genreChart' => $genreChart,
            'labelThemeChart' => $labelThemeChart,
            'themeChart' => $themeChart,
            'navLinkId' => 'anime-stat',
        ]);
    }
}
