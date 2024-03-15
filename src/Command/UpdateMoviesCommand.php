<?php

namespace App\Command;

use App\Repository\MovieGenreRepository;
use App\Repository\MovieRepository;
use App\Service\StrSpecialCharsLower;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:update-movies',
    description: 'Pour mettres les films à jour',
)]
class UpdateMoviesCommand extends Command
{

    private MovieRepository $movieRepository;

    private MovieGenreRepository $movieGenreRepository;

    private ObjectManager $manager;

    private KernelInterface $kernel;

    private StrSpecialCharsLower $strSpecialCharsLower;


    public function __construct(MovieRepository $movieRepository, MovieGenreRepository $movieGenreRepository, ManagerRegistry $managerRegistry, KernelInterface $kernel, StrSpecialCharsLower $strSpecialCharsLower)
    {

        parent::__construct();
        $this->movieRepository = $movieRepository;
        $this->movieGenreRepository = $movieGenreRepository;
        $this->manager = $managerRegistry->getManager();
        $this->kernel = $kernel;
        $this->strSpecialCharsLower = $strSpecialCharsLower;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $movies = $this->movieRepository->findBy(['updated' => false]);

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

            $movie->setSlug($this->strSpecialCharsLower->serie($movie->getName()));

            foreach ($data['genres'] as $genre){

                $addGenre = $this->movieGenreRepository->findOneBy(['name' => $genre['name']]);

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

            $projectDir = $this->kernel->getProjectDir();

            // Chemin où enregistrer l'image
            $cheminImageDestination = "/public/image/movie/poster/" . $movie->getSlug().'.jpeg';

            // Téléchargement et enregistrement de l'image
            if (imagejpeg($cover, $projectDir . $cheminImageDestination, 100)) {
                $movie->setArtwork($cheminImageDestination);
            } else {
                $movie->setArtwork(null);
            }

            $movie->setUpdated(true);

            $this->manager->persist($movie);
            $this->manager->flush();

        }

        return Command::SUCCESS;
    }
}
