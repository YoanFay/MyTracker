<?php

namespace App\Command;

use App\Repository\MovieGenreRepository;
use App\Repository\MovieRepository;
use App\Service\StrSpecialCharsLower;
use App\Service\TMDBService;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

    private TMDBService $TMDBService;


    public function __construct(MovieRepository $movieRepository, MovieGenreRepository $movieGenreRepository, ManagerRegistry $managerRegistry, KernelInterface $kernel, StrSpecialCharsLower $strSpecialCharsLower, TMDBService $TMDBService)
    {

        parent::__construct();
        $this->movieRepository = $movieRepository;
        $this->movieGenreRepository = $movieGenreRepository;
        $this->manager = $managerRegistry->getManager();
        $this->kernel = $kernel;
        $this->strSpecialCharsLower = $strSpecialCharsLower;
        $this->TMDBService = $TMDBService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $movies = $this->movieRepository->findBy(['updated' => false]);

        foreach ($movies as $movie) {

            $data = $this->TMDBService->getData('/movie/'.$movie->getTmdbId().'?language=fr-FR');

            $movie->setName($data['title']);

            $releaseDate = DateTime::createFromFormat('Y-m-d', $data['release_date']);

            if($releaseDate) {
                $movie->setReleaseDate($releaseDate);
            }

            $movie->setSlug($this->strSpecialCharsLower->serie($movie->getName()));

            foreach ($data['genres'] as $genre){

                $addGenre = $this->movieGenreRepository->findOneBy(['name' => $genre['name']]);

                $movie->addMovieGenre($addGenre);

            }

            $data = $this->TMDBService->getData('/movie/'.$movie->getTmdbId().'/images?include_image_language=fr');

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
