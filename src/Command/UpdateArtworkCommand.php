<?php

namespace App\Command;

use App\Repository\SerieRepository;
use App\Service\TVDBService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateArtworkCommand extends Command
{

    private SerieRepository $serieRepository;

    private ObjectManager $manager;

    private TVDBService $TVDBService;


    public function __construct(SerieRepository $serieRepository, ManagerRegistry $managerRegistry, TVDBService $TVDBService)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->manager = $managerRegistry->getManager();
        $this->TVDBService = $TVDBService;
    }


    protected function configure(): void
    {

        $this->setName('app:update-artwork');
        $this->setDescription('Pour les images des séries');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $series = $this->serieRepository->findArtworkId();

        print_r("J'ai récupérer les séries");

        foreach ($series as $serie) {

            print_r("Je test une série");

            $this->TVDBService->updateArtwork($serie);

            $this->manager->persist($serie);
            $this->manager->flush();
        }

        return Command::SUCCESS;
    }
}
