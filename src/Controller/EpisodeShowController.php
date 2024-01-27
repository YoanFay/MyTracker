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

class EpisodeShowController extends AbstractController
{
    #[Route('/episode', name: 'episode')]
    public function index(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository, MovieRepository $MovieRepository): Response
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
            
            switch($episode->getSerie()->getType()){
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

        return $this->render('episode_show/index.html.twig', [
            'series' => $showSerie,
            'episodes' => $episodes,
            'episodesByDate' => $episodesByDate,
            'dateKeys' => $dateKeys,
            'timeByDateType' => $timeByDateType,
            'globalDuration' => $globalDuration,
            'globalDurationAnime' => $globalDurationAnime,
            'globalDurationSerie' => $globalDurationSerie,
            'globalDurationReplay' => $globalDurationReplay,
        ]);
    }

    #[Route('/episode/add', name: 'episode_add')]
    public function addEpisode(ManagerRegistry $managerRegistry, UsersRepository $usersRepository, Request $request): Response
    {

        $episode = new EpisodeShow();

        $form = $this->createForm(EpisodeShowType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $passwordTest = $form->get('password')->getData();

            if (!password_verify($passwordTest, $_ENV['PASSWORD_USER'])){
                $this->addFlash('error', 'Mot de passe incorrect');

                return $this->redirectToRoute('episode_add');
            }

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
        ]);
    }
}
