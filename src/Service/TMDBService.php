<?php

namespace App\Service;

use App\Entity\Movie;
use App\Entity\MovieGenre;
use App\Repository\MovieGenreRepository;
use DateTime;
use GuzzleHttp\Client;
use Symfony\Component\HttpKernel\KernelInterface;

class TMDBService
{

    private MovieGenreRepository $movieGenreRepository;

    private KernelInterface $kernel;

    private const TOKEN = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJhZmI1ZDg4MTM3ZTM4OWU2M2M4YjVmNDVmNWRhMTg2ZSIsInN1YiI6IjY1NzcwNmEyNTY0ZWM3MDBmZWI1NDA3NiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.B8eXCk-bwC32V5dHtwmtIXl1urYEfCYR0LCeOnckGos';

    public function __construct(MovieGenreRepository $movieGenreRepository, KernelInterface $kernel)
    {
        $this->movieGenreRepository = $movieGenreRepository;
        $this->kernel = $kernel;
    }


    public function updateInfo(Movie $movie): void
    {

        $strSpecialCharsLower = new StrSpecialCharsLower();

        $data = self::getData('/movie/'.$movie->getTmdbId().'?language=fr-FR');

        $movie->setName($data['title']);

        $releaseDate = DateTime::createFromFormat('Y-m-d', $data['release_date']);

        if ($releaseDate) {
            $movie->setReleaseDate($releaseDate);
        }

        $movie->setSlug($strSpecialCharsLower->serie($movie->getName()));

        foreach ($data['genres'] as $genre) {

            /** @var MovieGenre $addGenre */
            $addGenre = $this->movieGenreRepository->findOneBy(['name' => $genre['name']]);

            $movie->addMovieGenre($addGenre);

        }

        if(!$movie->getDuration()){

            $duration = $data['runtime'] * 60000;

            $movie->setDuration($duration);

        }

        $movie = $this->updateArtwork($movie);

        $movie->setUpdated(true);

    }

    public function updateArtwork(Movie $movie): Movie
    {

        $data = self::getData('/movie/'.$movie->getTmdbId().'/images?include_image_language=fr');

        // Lien de l'image à télécharger
        $lienImage = "https://image.tmdb.org/t/p/w600_and_h900_bestv2".$data['posters'][0]['file_path'];

        /** @var string $dataImage */
        $dataImage = file_get_contents($lienImage);

        $cover = imagecreatefromstring($dataImage);

        $projectDir = $this->kernel->getProjectDir();

        // Chemin où enregistrer l'image
        $cheminImageDestination = "/public/image/movie/poster/".$movie->getSlug().'.jpeg';

        // Téléchargement et enregistrement de l'image
        if ($cover && imagejpeg($cover, $projectDir.$cheminImageDestination, 100)) {
            $movie->setArtwork($cheminImageDestination);
        } else {
            $movie->setArtwork(null);
        }

        return $movie;

    }


    public function getData(string $url): mixed
    {

        $client = new Client();

        try {
            $response = $client->get("https://api.themoviedb.org/3".$url, [
                'headers' => [
                    'Authorization' => 'Bearer '.self::TOKEN,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            $data = null;
        }

        return $data;
    }

}