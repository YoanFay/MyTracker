<?php

namespace App\Command;

use App\Repository\SerieRepository;
use App\Service\AniListService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateAnimeEmptyScore extends Command
{

    private SerieRepository $serieRepository;
    
    private AniListService $aniListService;


    public function __construct(SerieRepository $serieRepository, AniListService $aniListService)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->aniListService = $aniListService;
    }


    protected function configure(): void
    {

        $this->setName('app:update-empty-score');
        $this->setDescription('Update le score des animes sans score');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $animes = $this->serieRepository->findAnime();

        foreach ($animes as $anime){

            $this->aniListService->setScore($anime);

        }

        return Command::SUCCESS;
    }
}
