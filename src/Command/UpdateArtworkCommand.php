<?php

namespace App\Command;

use App\Entity\EpisodeShow;
use App\Repository\EpisodeShowRepository;
use App\Repository\SerieRepository;
use App\Service\TVDBService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;


class UpdateArtworkCommand extends Command
{

    private SerieRepository $serieRepository;

    private ObjectManager $manager;
    
    private KernelInterface $kernel;

    private TVDBService $TVDBService;


    public function __construct(SerieRepository $serieRepository, ManagerRegistry $managerRegistry, KernelInterface $kernel, TVDBService $TVDBService)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->manager = $managerRegistry->getManager();
        $this->kernel = $kernel;
        $this->TVDBService = $TVDBService;
    }


    protected function configure(): void
    {

        $this->setName('app:update-artwork');
        $this->setDescription('Pour les images des séries');
    }


    /**
     * @throws GuzzleException|NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $series = $this->serieRepository->findArtworkId();

        foreach ($series as $serie) {

            $this->TVDBService->updateArtwork($serie);

            $this->manager->persist($serie);
            $this->manager->flush();
        }

        return Command::SUCCESS;
    }
}
