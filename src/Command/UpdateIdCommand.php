<?php

namespace App\Command;

use App\Entity\Episode;
use App\Repository\EpisodeRepository;
use App\Repository\SerieRepository;
use App\Service\TVDBService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateIdCommand extends Command
{

    private SerieRepository $serieRepository;

    private EpisodeRepository $episodeRepository;

    private ObjectManager $manager;

    private TVDBService $TVDBService;


    public function __construct(SerieRepository $serieRepository, EpisodeRepository $episodeRepository, ManagerRegistry $managerRegistry, TVDBService $TVDBService)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->episodeRepository = $episodeRepository;
        $this->manager = $managerRegistry->getManager();
        $this->TVDBService = $TVDBService;
    }


    protected function configure(): void
    {

        $this->setName('app:update-id');
        $this->setDescription('Pour les ID des épisodes et des séries');
    }


    /**
     * @throws GuzzleException|NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $series = $this->serieRepository->findNotTvdbId();

        foreach ($series as $serie) {
            $data = null;
            $episode = null;
            /** @var ?Episode $episode */
            $episode = $this->episodeRepository->findBySerie($serie);

            if ($episode && $episode->getTvdbId()) {

                $data = $this->TVDBService->getData("/episodes/".$episode->getTvdbId());

                $serie->setTvdbId($data['data']['seriesId']);

                $this->manager->persist($serie);
                $this->manager->flush();
            }
        }

        $episodesWithoutTVDB = $this->episodeRepository->findWitoutTVDB();

        if ($episodesWithoutTVDB !== []) {
            foreach ($episodesWithoutTVDB as $episodeWithoutTVDB) {
                $data3 = null;

                if ($episodeWithoutTVDB->getSerie()->getTvdbId()) {

                    $data3 = $this->TVDBService->getData("/series/".$episodeWithoutTVDB->getSerie()->getTvdbId()."/episodes/default?page=1&season=".$episodeWithoutTVDB->getSaisonNumber()."&episodeNumber=".$episodeWithoutTVDB->getEpisodeNumber());

                    $episodeWithoutTVDB->setTvdbId($data3['data']['episodes'][0]['id']);

                    $this->manager->persist($episodeWithoutTVDB);
                    $this->manager->flush();
                }

            }
        }

        return Command::SUCCESS;
    }
}
