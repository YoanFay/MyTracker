<?php

namespace App\Command;

use App\Repository\SerieRepository;
use App\Service\UpdateDateService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateDateCommand extends Command
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

        $this->setName('app:update-date');
        $this->setDescription('Pour les date des épisodes et des séries');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $animes = $this->serieRepository->noFirstAiredAnime();
        dump("------------------------------------------------ updateFirstAiredAnime ------------------------------------------------");

        foreach ($animes as $anime) {

            $this->updateDateService->updateFirstAiredAnime($anime);
            
        }

        $animes = $this->serieRepository->updateAiredAnime();
        dump("------------------------------------------------ updateAiredAnime ------------------------------------------------");

        foreach ($animes as $anime) {

            $this->updateDateService->updateAiredAnime($anime);
            
        }

        $animes = $this->serieRepository->getAnimeWithoutLastDate();
        dump("------------------------------------------------ updateLastAiredAnime ------------------------------------------------");

        foreach ($animes as $anime) {

            $this->updateDateService->updateLastAiredAnime($anime);

        }

        $series = $this->serieRepository->noFirstAired();
        dump("------------------------------------------------ updateFirstAired ------------------------------------------------");

        foreach ($series as $serie){

            $this->updateDateService->updateFirstAired($serie);

        }

        $series = $this->serieRepository->updateAired();
        dump("------------------------------------------------ updateAired ------------------------------------------------");

        foreach ($series as $serie){

            $this->updateDateService->updateAired($serie);

        }

        return Command::SUCCESS;
    }
}
