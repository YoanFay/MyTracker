<?php

namespace App\Controller;

use App\Entity\EpisodeShow;
use App\Form\EpisodeShowType;
use App\Repository\UsersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SerieRepository;
use App\Repository\EpisodeShowRepository;
use App\Repository\MovieRepository;
use Bugsnag\BugsnagBundle\DependencyInjection\ClientFactory;
use Bugsnag\Client;
use DateTime;

class EpisodeShowController extends AbstractController
{
    #[Route('/episode', name: 'episode')]
    public function index(EpisodeShowRepository $episodeShowRepository): Response
    {
        
        $listMonth = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre'
        ];
        
        $dates = $episodeShowRepository->findMonth();
        
        $listDate = [];
        $time = [];
        $saveYear = explode('-', $dates[0]['DATE'])[0];
        $total = 0;
        
        foreach ($dates as $date) {
            
            $explode = explode('-', $date['DATE']);
            
            $year = $explode[0];
            $month = $explode[1];
            
            if($year !== $saveYear){
                $listDate[$saveYear] = $time;
                $listDate[$saveYear]['Total'] = $total;
                
                $saveYear = $year;
                $time = [];
                $total = 0;
            }
            
            if(!array_key_exists($listMonth[$month], $time)){
                $time[$listMonth[$month]] = [
                    'Anime' => 0,
                    'Séries' => 0,
                    'Replay' => 0,
                    'Total' => 0,
                    'ID' => $month,
                ];
            }
            
            $time[$listMonth[$month]][$date['TYPE']] += $date['DURATION'];
            $time[$listMonth[$month]]['Total'] += $date['DURATION'];
            $total += $date['DURATION'];
            
            /*$time = [
                'name'=> $listMonth[$month],
                'duration'=> $date['DURATION'],
            ];*/
            
        }
        
        $listDate[$saveYear] = $time;
        $listDate[$saveYear]['Total'] = $total;
        
        return $this->render('episode_show\index.html.twig', [
            'list' => $listDate,
            'navLinkId' => 'episode',
        ]);
        
    }
    
    
    #[Route('/allEpisode', name: 'episode_all')]
    public function allEpisode(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository, MovieRepository $MovieRepository): Response
    {
        
        $series = $serieRepository->findAll();
        $episodes = $episodeShowRepository->findAll();
        $showSerie = [];
        
        foreach ($series as $serie) {
            if (count($serie->getEpisodeShows()->getValues()) > 0) {
                $showSerie[$serie->getName()] = $serie->getEpisodeShows()->getValues();
            }
        }
        
        $episodesByDate = [];
        $timeByDateType = [];
        $dateKeys = [];
        $globalDuration = 0;
        $globalDurationAnime = 0;
        $globalDurationSerie = 0;
        $globalDurationReplay = 0;
        
        foreach ($episodes as $episode) {
            $dateKey = $episode->getShowDate()->format("Y-m-d");
            
            $globalDuration += $episode->getDuration();
            
            switch ($episode->getSerie()->getType()) {
                case 'Anime':
                    $globalDurationAnime += $episode->getDuration();
                    break;
                    case 'Séries':
                        $globalDurationSerie += $episode->getDuration();
                        break;
                        case 'Replay':
                            $globalDurationReplay += $episode->getDuration();
                            break;
                            
                        }
                        
                        if (!isset($episodesByDate[$dateKey])) {
                            $episodesByDate[$dateKey] = [$episode];
                            $dateKeys[] = $dateKey;
                        } else {
                            $episodesByDate[$dateKey][] = $episode;
                        }
                        
                        if (!isset($timeByDateType[$dateKey])) {
                            $timeByDateType[$dateKey] = [
                                'Anime' => 0,
                                'Séries' => 0,
                                'Replay' => 0
                            ];
                        }
                        
                        $timeByDateType[$dateKey][$episode->getSerie()->getType()] += $episode->getDuration();
                    }
                    
                    return $this->render('episode_show/allEpisode.html.twig', [
                        'series' => $showSerie,
                        'episodes' => $episodes,
                        'episodesByDate' => $episodesByDate,
                        'dateKeys' => $dateKeys,
                        'timeByDateType' => $timeByDateType,
                        'globalDuration' => $globalDuration,
                        'globalDurationAnime' => $globalDurationAnime,
                        'globalDurationSerie' => $globalDurationSerie,
                        'globalDurationReplay' => $globalDurationReplay,
                        'navLinkId' => 'episode',
                    ]);
                }
                
                
                #[Route('/episode/add', name: 'episode_add')]
                public function addEpisode(ManagerRegistry $managerRegistry, UsersRepository $usersRepository, Request $request): Response
                {
                    
                    $episode = new EpisodeShow();
                    
                    $form = $this->createForm(EpisodeShowType::class, $episode);
                    $form->handleRequest($request);
                    
                    if ($form->isSubmitted() && $form->isValid()) {
                        
                        $user = $usersRepository->findOneBy(['plexName' => 'yoan.f8']);
                        
                        $episode->setUser($user);
                        
                        $episode->setDuration($episode->getDuration() * 60000);
                        
                        $managerRegistry->getManager()->persist($episode);
                        $managerRegistry->getManager()->flush();
                        
                        return $this->redirectToRoute('episode');
                    }
                    
                    return $this->render('episode_show/add.html.twig', [
                        'controller_name' => 'EpisodeShowController',
                        'form' => $form->createView(),
                        'navLinkId' => 'episode-add',
                    ]);
                }
                
                
                #[Route('/episode/categorie/{categorie}', name: 'episode_categories')]
                public function episodeCategories(EpisodeShowRepository $episodeShowRepository, $categorie): Response
                {
                    
                    switch ($categorie) {
                        case 'anime':
                            $episodes = $episodeShowRepository->findAnime();
                            $title = "Anime";
                            $text = "d'animes";
                            $nav = "anime";
                            break;
                            case 'serie':
                                $episodes = $episodeShowRepository->findSerie();
                                $title = "Série";
                                $text = "de séries";
                                $nav = "serie";
                                break;
                                case 'replay':
                                    $episodes = $episodeShowRepository->findReplay();
                                    $title = "Replay";
                                    $text = "de replay";
                                    $nav = "replay";
                                    break;
                                    default:
                                    $episodes = $episodeShowRepository->findAnime();
                                    $title = "Anime";
                                    $text = "d'animes";
                                    $nav = "anime";
                                }
                                
                                $episodesByDate = [];
                                $dateKeys = [];
                                $globalDuration = 0;
                                
                                foreach ($episodes as $episode) {
                                    $dateKey = $episode->getShowDate()->format("Y-m-d");
                                    
                                    $globalDuration += $episode->getDuration();
                                    
                                    if (!isset($episodesByDate[$dateKey])) {
                                        $episodesByDate[$dateKey] = [$episode];
                                        $dateKeys[] = $dateKey;
                                    } else {
                                        $episodesByDate[$dateKey][] = $episode;
                                    }
                                }
                                
                                return $this->render('episode_show/categories.html.twig', [
                                    'episodes' => $episodes,
                                    'episodesByDate' => $episodesByDate,
                                    'dateKeys' => $dateKeys,
                                    'globalDuration' => $globalDuration,
                                    'title' => $title,
                                    'text' => $text,
                                    'navLinkId' => $nav,
                                ]);
                            }
                            
                            #[Route('/episode/{year}/{month}', name: 'episode_date')]
                            public function episodeDate(EpisodeShowRepository $episodeShowRepository, SerieRepository $serieRepository, $year = 0, $month = 0): Response
                            {
                                $currentDate = new DateTime();
                                $testCurrent = false;
                                
                                if($year === 0){
                                    $year = '%';
                                }
                                
                                if($month === 0){
                                    $month = '%';
                                    $startDate = new DateTime($year.'-01-01');
                                    $endDate = new DateTime($year.'-12-31');
                                    $testCurrent = true;
                                }else{
                                    $startDate = new DateTime($year.'-'.$month.'-01');
                                    $endDate = new DateTime($startDate->format($year.'-'.$month.'-t'));
                                }

                                if($endDate > $currentDate && ($endDate->format('m') === $currentDate->format('m') || $testCurrent)) {
                                    $endDate = $currentDate;
                                }

                                $daysSinceStartOfYear = $startDate->diff($endDate)->days + 1;
                                
                                $series = $serieRepository->findAll();
                                $episodes = $episodeShowRepository->findByDate($year, $month);
                                $showSerie = [];
                                
                                $listMonth = [
                                    '%' => '',
                                    '01' => 'Janvier',
                                    '02' => 'Février',
                                    '03' => 'Mars',
                                    '04' => 'Avril',
                                    '05' => 'Mai',
                                    '06' => 'Juin',
                                    '07' => 'Juillet',
                                    '08' => 'Août',
                                    '09' => 'Septembre',
                                    '10' => 'Octobre',
                                    '11' => 'Novembre',
                                    '12' => 'Décembre'
                                ];
                                
                                $month = $listMonth[$month];
                                
                                foreach ($series as $serie) {
                                    if (count($serie->getEpisodeShows()->getValues()) > 0) {
                                        $showSerie[$serie->getName()] = $serie->getEpisodeShows()->getValues();
                                    }
                                }
                                
                                $episodesByDate = [];
                                $timeByDateType = [];
                                $dateKeys = [];
                                $globalDuration = 0;
                                $globalDurationAnime = 0;
                                $globalDurationSerie = 0;
                                $globalDurationReplay = 0;
                                
                                foreach ($episodes as $episode) {
                                    $dateKey = $episode->getShowDate()->format("Y-m-d");
                                    
                                    $globalDuration += $episode->getDuration();
                                    
                                    switch ($episode->getSerie()->getType()) {
                                        case 'Anime':
                                            $globalDurationAnime += $episode->getDuration();
                                            break;
                                            case 'Séries':
                                                $globalDurationSerie += $episode->getDuration();
                                                break;
                                                case 'Replay':
                                                    $globalDurationReplay += $episode->getDuration();
                                                    break;
                                                    
                                                }
                                                
                                                if (!isset($episodesByDate[$dateKey])) {
                                                    $episodesByDate[$dateKey] = [$episode];
                                                    $dateKeys[] = $dateKey;
                                                } else {
                                                    $episodesByDate[$dateKey][] = $episode;
                                                }
                                                
                                                if (!isset($timeByDateType[$dateKey])) {
                                                    $timeByDateType[$dateKey] = [
                                                        'Anime' => 0,
                                                        'Séries' => 0,
                                                        'Replay' => 0
                                                    ];
                                                }
                                                
                                                $timeByDateType[$dateKey][$episode->getSerie()->getType()] += $episode->getDuration();
                                            }
                                            
                                            return $this->render('episode_show/episodeDate.html.twig', [
                                                'year' => $year,
                                                'month' => $month,
                                                'series' => $showSerie,
                                                'episodes' => $episodes,
                                                'episodesByDate' => $episodesByDate,
                                                'dateKeys' => $dateKeys,
                                                'timeByDateType' => $timeByDateType,
                                                'globalDuration' => $globalDuration,
                                                'globalDurationAnime' => $globalDurationAnime,
                                                'globalDurationSerie' => $globalDurationSerie,
                                                'globalDurationReplay' => $globalDurationReplay,
                                                'daysSinceStartOfYear' => $daysSinceStartOfYear,
                                                'navLinkId' => 'episode',
                                            ]);
                                        }
                                    }
                                    