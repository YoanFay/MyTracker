<?php

namespace App\Controller;

use GuzzleHttp\Client;
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


    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[Route('/movie/test', name: 'movie_test')]
    public function test(MovieRepository $movieRepository): Response
    {

        $movies = $movieRepository->findAll();

        foreach ($movies as $movie) {

            $client = new Client();

            $apiUrl = 'https://api.themoviedb.org/3';

            $apiKey = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJhZmI1ZDg4MTM3ZTM4OWU2M2M4YjVmNDVmNWRhMTg2ZSIsInN1YiI6IjY1NzcwNmEyNTY0ZWM3MDBmZWI1NDA3NiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.B8eXCk-bwC32V5dHtwmtIXl1urYEfCYR0LCeOnckGos';

            $response = $client->get($apiUrl.'/movie/'.$movie->getTmdbId().'?language=fr-FR', [
                'headers' => [
                    'Authorization' => 'Bearer '.$apiKey,
                    'Accept' => 'application/json',
                ],
            ]);


            $data = json_decode($response->getBody(), true);

            dump($data['title']);

            $genres = [];

            foreach ($data['genres'] as $genre){

                $genres[] = $genre['name'];

            }

            dump($genres);

            $response = $client->get($apiUrl.'/movie/'.$movie->getTmdbId().'/images?include_image_language=fr', [
                'headers' => [
                    'Authorization' => 'Bearer '.$apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $poster = $data['posters'][0]['file_path'];

            dump($poster);

        }

        dd('Fin Test');

    }
}
