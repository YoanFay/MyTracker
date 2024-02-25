<?php

namespace App\Controller;

use App\Repository\MovieGenreRepository;
use App\Service\StrSpecialCharsLower;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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
    public function test(MovieRepository $movieRepository, MovieGenreRepository $movieGenreRepository, StrSpecialCharsLower  $strSpecialCharsLower, KernelInterface $kernel, ManagerRegistry $managerRegistry): Response
    {

        $movies = $movieRepository->findBy(['updated', false]);

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

            $movie->setName($data['title']);

            $movie->setSlug($strSpecialCharsLower->serie($movie->getName()));

            foreach ($data['genres'] as $genre){

                $addGenre = $movieGenreRepository->findOneBy(['name' => $genre['name']]);

                $movie->addMovieGenre($addGenre);

            }

            $response = $client->get($apiUrl.'/movie/'.$movie->getTmdbId().'/images?include_image_language=fr', [
                'headers' => [
                    'Authorization' => 'Bearer '.$apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            // Lien de l'image à télécharger
            $lienImage = "https://image.tmdb.org/t/p/w600_and_h900_bestv2".$data['posters'][0]['file_path'];

            $cover = imagecreatefromstring(file_get_contents($lienImage));

            $projectDir = $kernel->getProjectDir();

            // Chemin où enregistrer l'image
            $cheminImageDestination = "/public/image/movie/poster/" . $movie->getSlug().'.jpeg';

            // Téléchargement et enregistrement de l'image
            if (imagejpeg($cover, $projectDir . $cheminImageDestination, 100)) {
                $movie->setArtwork($cheminImageDestination);
            } else {
                $movie->setArtwork(null);
            }

            $managerRegistry->getManager()->persist($movie);
            $managerRegistry->getManager()->flush();

        }

        dd('Fin Test');

    }
}
