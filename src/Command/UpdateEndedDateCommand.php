<?php

namespace App\Command;

use App\Repository\SerieRepository;
use App\Service\UpdateDateService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateEndedDateCommand extends Command
{

    private SerieRepository $serieRepository;
    
    private UpdateDateService $updateDateService;


    public function __construct(SerieRepository $serieRepository, UpdateDateService $updateDateService)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->updateDateService = $updateDateService;
    }


    protected function configure(): void
    {

        $this->setName('app:update-ended-date');
        $this->setDescription('Pour les date des épisodes et des séries terminées');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $date = new \DateTimeImmutable();

        $date = $date->modify('-6 months');

        $animes = $this->serieRepository->endedAnimeBeforeDate($date);
        dump("------------------------------------------------ updateEndedAnime ------------------------------------------------");

        foreach ($animes as $anime) {

            $this->updateDateService->updateEndedAnime($anime);
            
        }

        $series = $this->serieRepository->ended();
        dump("------------------------------------------------ updateEnded ------------------------------------------------");

        foreach ($series as $serie){

            $this->updateDateService->updateEnded($serie);

        }

        return Command::SUCCESS;
    }
}
