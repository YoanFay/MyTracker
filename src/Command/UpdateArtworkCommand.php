<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Repository\SerieRepository;
use App\Service\TMDBService;
use App\Service\TVDBService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateArtworkCommand extends Command
{

    private SerieRepository $serieRepository;

    private MovieRepository $movieRepository;

    private ObjectManager $manager;

    private TVDBService $TVDBService;

    private TMDBService $TMDBService;


    public function __construct(SerieRepository $serieRepository, MovieRepository $movieRepository, ManagerRegistry $managerRegistry, TVDBService $TVDBService, TMDBService $TMDBService)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->movieRepository = $movieRepository;
        $this->manager = $managerRegistry->getManager();
        $this->TVDBService = $TVDBService;
        $this->TMDBService = $TMDBService;
    }


    protected function configure(): void
    {

        $this->setName('app:update-artwork');
        $this->setDescription('Pour les images des séries');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $series = $this->serieRepository->findArtworkId();

        foreach ($series as $serie) {

            $this->TVDBService->updateArtwork($serie);

            $this->manager->persist($serie);
            $this->manager->flush();
        }

        $movies = $this->movieRepository->getNoArtwork();

        foreach ($movies as $movie) {

            $this->TMDBService->updateArtwork($movie);

            $this->manager->persist($movie);
            $this->manager->flush();
        }

        return Command::SUCCESS;
    }
}
