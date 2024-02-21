<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;

class MovieController extends AbstractController
{
    #[Route('/movie', name: 'movie')]
    public function index(MovieRepository $movieRepository): Response
    {

        $movies = $movieRepository->findAll();

        $moviesByDate = [];
        $dateKeys = [];

        foreach ($movies as $movie) {
            $dateKey = $movie->getShowDate()->format("Y-m-d");

            if (!isset($moviesByDate[$dateKey])) {
                $moviesByDate[$dateKey] = [$movie];
                $dateKeys[] = $dateKey;
            } else {
                $moviesByDate[$dateKey][] = $movie;
            }
        }
        
        return $this->render('movie/index.html.twig', [
            'moviesByDate' => $moviesByDate,
            'dateKeys' => $dateKeys,
            'controller_name' => 'MovieController',
            'navLinkId' => 'movie',
        ]);
    }
}
