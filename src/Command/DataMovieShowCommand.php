<?php

namespace App\Command;

use App\Entity\EpisodeShow;
use App\Entity\MovieShow;
use App\Repository\EpisodeRepository;
use App\Repository\MovieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:data-movie-show',
    description: 'Update movie to movie show',
)]
class DataMovieShowCommand extends Command
{

    private MovieRepository $movieRepository;

    private ObjectManager $manager;


    public function __construct(MovieRepository $movieRepository, ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->movieRepository = $movieRepository;
        $this->manager = $managerRegistry->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $movies = $this->movieRepository->findByDateNotNull();

        foreach ($movies as $movie) {

            $movieShow = new MovieShow();
            $movieShow->setMovie($movie);
            $movieShow->setShowDate($movie->getShowDate());

            $movie->setShowDate(null);

            $this->manager->persist($movieShow);
            $this->manager->persist($movie);
            $this->manager->flush();

        }

        return Command::SUCCESS;
    }
}
