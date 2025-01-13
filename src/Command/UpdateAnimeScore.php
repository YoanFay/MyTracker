<?php

namespace App\Command;

use App\Repository\SerieRepository;
use App\Service\AniListService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateAnimeScore extends Command
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

        $this->setName('app:update-score');
        $this->setDescription('Pour les date des épisodes et des séries');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { stats { scoreDistribution {score, amount}}}}';

        $animes = $this->serieRepository->findAnime();

        foreach ($animes as $anime){

            $result = $this->aniListService->getData($query, $anime);

            dd($result);

        }

        return Command::SUCCESS;
    }
}
