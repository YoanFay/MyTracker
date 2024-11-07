<?php

namespace App\Controller;

use App\Repository\EpisodeRepository;
use App\Repository\EpisodeShowRepository;
use App\Repository\MangaRepository;
use App\Repository\MangaTomeRepository;
use App\Repository\MovieShowRepository;
use App\Service\StatService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stat')]
class StatsController extends AbstractController
{

    /**
     * @throws Exception
     */
    #[Route('/historique', name: 'historique_stat')]
    public function historiqueStat(StatService $statService, MovieShowRepository $movieShowRepository, EpisodeRepository $episodeRepository, EpisodeShowRepository $episodeShowRepository): Response
    {

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $monthsOfYear = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        $movieDuration = $movieShowRepository->getDuration();
        $durations = $episodeRepository->getDurationByType();

        $durationsByType = [
            'Anime' => 0,
            'Séries' => 0,
            'Replay' => 0
        ];

        foreach ($durations as $duration) {
            $durationsByType[$duration['TYPE']] = $duration['COUNT'];
        }

        $durationByGenre = $episodeRepository->getDurationGenre();
        $durationByTheme = $episodeRepository->getDurationTheme();
        $allEpisodesShow = $episodeShowRepository->findAll();
        $allMovies = $movieShowRepository->findAll();

        $animeByDay = $statService->initializeByPeriod($daysOfWeek);
        $serieByDay = $statService->initializeByPeriod($daysOfWeek);
        $replayByDay = $statService->initializeByPeriod($daysOfWeek);
        $movieByDay = $statService->initializeByPeriod($daysOfWeek);

        $animeByMonth = $statService->initializeByPeriod($monthsOfYear);
        $serieByMonth = $statService->initializeByPeriod($monthsOfYear);
        $replayByMonth = $statService->initializeByPeriod($monthsOfYear);
        $movieByMonth = $statService->initializeByPeriod($monthsOfYear);

        foreach ($allEpisodesShow as $episodeShow) {

            $episode = $episodeShow->getEpisode();
            $day = $episodeShow->getShowDate()->format('l');
            $month = $episodeShow->getShowDate()->format('F');
            $duration = $episode->getDuration();

            switch ($episode->getSerie()->getSerieType()->getName()) {
            case "Anime":
                $animeByDay[$day] += $duration;
                $animeByMonth[$month] += $duration;
                break;
            case "Séries":
                $serieByDay[$day] += $duration;
                $serieByMonth[$month] += $duration;
                break;
            case "Replay":
                $replayByDay[$day] += $duration;
                $replayByMonth[$month] += $duration;
                break;
            }
        }

        foreach ($allMovies as $movie) {
            $day = $movie->getShowDate()->format('l');
            $month = $movie->getShowDate()->format('F');
            $duration = $movie->getMovie()->getDuration();
            $movieByDay[$day] += $duration;
            $movieByMonth[$month] += $duration;
        }

        $aujourdHui = new DateTime();
        $debutSite = new DateTime($aujourdHui->format('2024-01-01'));

        $joursCount = $statService->initializeByPeriod($daysOfWeek);
        $moisCount = $statService->initializeByPeriod($monthsOfYear);
        $saveMonth = null;

        while ($debutSite <= $aujourdHui) {
            $jourSemaine = $debutSite->format('l');
            $moisAnnee = $debutSite->format('F');
            $joursCount[$jourSemaine]++;
            if ($saveMonth !== $moisAnnee) {
                $moisCount[$moisAnnee]++;
                $saveMonth = $moisAnnee;
            }
            $debutSite->modify('+1 day');
        }

        $statService->divideByPeriod($animeByDay, $joursCount);
        $statService->divideByPeriod($serieByDay, $joursCount);
        $statService->divideByPeriod($replayByDay, $joursCount);
        $statService->divideByPeriod($movieByDay, $joursCount);

        $statService->divideByPeriod($animeByMonth, $moisCount);
        $statService->divideByPeriod($serieByMonth, $moisCount);
        $statService->divideByPeriod($replayByMonth, $moisCount);
        $statService->divideByPeriod($movieByMonth, $moisCount);

        $timeChart = $statService->buildChart([$durationsByType['Anime'], $durationsByType['Séries'], $durationsByType['Replay'], $movieDuration['SUM']]);
        $animeByDayChart = $statService->buildChart($animeByDay);
        $serieByDayChart = $statService->buildChart($serieByDay);
        $replayByDayChart = $statService->buildChart($replayByDay);
        $movieByDayChart = $statService->buildChart($movieByDay);

        $animeByMonthChart = $statService->buildChart($animeByMonth);
        $serieByMonthChart = $statService->buildChart($serieByMonth);
        $replayByMonthChart = $statService->buildChart($replayByMonth);
        $movieByMonthChart = $statService->buildChart($movieByMonth);

        $genreChartData = $statService->buildLabelAndDataChart($durationByGenre);
        $themeChartData = $statService->buildLabelAndDataChart($durationByTheme);

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
            'labelGenreChart' => $genreChartData['labels'],
            'genreChart' => $genreChartData['values'],
            'labelThemeChart' => $themeChartData['labels'],
            'themeChart' => $themeChartData['values'],
            'navLinkId' => 'historique_stat',
        ]);
    }


    /**
     * @throws Exception
     */
    #[Route('/manga', name: 'manga_stat')]
    public function mangaStat(StatService $statService, MangaRepository $mangaRepository, MangaTomeRepository $mangaTomeRepository): Response
    {

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $monthsOfYear = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        $allMangaTome = $mangaTomeRepository->findAll();

        $tomeStartByDay = $statService->initializeByPeriod($daysOfWeek);
        $tomeStartByMonth = $statService->initializeByPeriod($monthsOfYear);
        $tomeEndByDay = $statService->initializeByPeriod($daysOfWeek);
        $tomeEndByMonth = $statService->initializeByPeriod($monthsOfYear);

        foreach ($allMangaTome as $tome) {
            if ($tome->getReadingStartDate()) {
                $day = $tome->getReadingStartDate()->format('l');
                $month = $tome->getReadingStartDate()->format('F');
                $tomeStartByDay[$day]++;
                $tomeStartByMonth[$month]++;
                if ($tome->getReadingEndDate()) {
                    $day = $tome->getReadingEndDate()->format('l');
                    $month = $tome->getReadingEndDate()->format('F');
                    $tomeEndByDay[$day]++;
                    $tomeEndByMonth[$month]++;
                }
            }
        }

        $aujourdHui = new DateTime();
        $debutSite = new DateTime($aujourdHui->format('2024-01-01'));

        $joursCount = $statService->initializeByPeriod($daysOfWeek);
        $moisCount = $statService->initializeByPeriod($monthsOfYear);
        $saveMonth = null;

        while ($debutSite <= $aujourdHui) {
            $jourSemaine = $debutSite->format('l');
            $moisAnnee = $debutSite->format('F');
            $joursCount[$jourSemaine]++;
            if ($saveMonth !== $moisAnnee) {
                $moisCount[$moisAnnee]++;
                $saveMonth = $moisAnnee;
            }
            $debutSite->modify('+1 day');
        }

        $statService->divideByPeriod($tomeStartByDay, $joursCount);
        $statService->divideByPeriod($tomeStartByMonth, $moisCount);
        $statService->divideByPeriod($tomeEndByDay, $joursCount);
        $statService->divideByPeriod($tomeEndByMonth, $moisCount);

        $tomeStartByDayChart = $statService->buildChart($tomeStartByDay);
        $tomeStartByMonthChart = $statService->buildChart($tomeStartByMonth);
        $tomeEndByDayChart = $statService->buildChart($tomeEndByDay);
        $tomeEndByMonthChart = $statService->buildChart($tomeEndByMonth);

        $mangaTomesByGenre = $mangaRepository->getMangaTomeByGenre();
        $mangaTomesByTheme = $mangaRepository->getMangaTomeByTheme();

        $genreChartData = $statService->buildLabelAndDataChart($mangaTomesByGenre);
        $themeChartData = $statService->buildLabelAndDataChart($mangaTomesByTheme);

        return $this->render('stats/manga.html.twig', [
            'tomeStartByDayChart' => $tomeStartByDayChart,
            'tomeStartByMonthChart' => $tomeStartByMonthChart,
            'tomeEndByDayChart' => $tomeEndByDayChart,
            'tomeEndByMonthChart' => $tomeEndByMonthChart,
            'labelGenreChart' => $genreChartData['labels'],
            'genreChart' => $genreChartData['values'],
            'labelThemeChart' => $themeChartData['labels'],
            'themeChart' => $themeChartData['values'],
            'navLinkId' => 'manga_stat',
        ]);
    }


    #[Route('/anime', name: 'anime_stat')]
    public function animeStat(StatService $statService, EpisodeRepository $episodeRepository): Response
    {

        $durationByGenre = $episodeRepository->getDurationGenre();
        $durationByTheme = $episodeRepository->getDurationTheme();

        $genreChartData = $statService->buildLabelAndDataChart($durationByGenre);
        $themeChartData = $statService->buildLabelAndDataChart($durationByTheme);

        return $this->render('stats/anime.html.twig', [
            'labelGenreChart' => $genreChartData['labels'],
            'genreChart' => $genreChartData['values'],
            'labelThemeChart' => $themeChartData['labels'],
            'themeChart' => $themeChartData['values'],
            'navLinkId' => 'anime_stat',
        ]);
    }
}
                    