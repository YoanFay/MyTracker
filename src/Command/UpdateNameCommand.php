<?php

namespace App\Command;

use App\Repository\EpisodeShowRepository;
use App\Repository\SerieRepository;
use App\Service\TVDBService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateNameCommand extends Command
{

    private SerieRepository $serieRepository;

    private EpisodeShowRepository $episodeShowRepository;

    private ObjectManager $manager;

    private TVDBService $TVDBService;


    public function __construct(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository, ManagerRegistry $managerRegistry, TVDBService $TVDBService)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->episodeShowRepository = $episodeShowRepository;
        $this->manager = $managerRegistry->getManager();
        $this->TVDBService = $TVDBService;
    }


    protected function configure(): void
    {

        $this->setName('app:update-name');
        $this->setDescription('Pour les noms français des séries');
    }


    /**
     * @throws GuzzleException|NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $series = $this->serieRepository->findTvdbId();

        foreach ($series as $serie) {

            try {

                $data = $this->TVDBService->getData("/series/".$serie->getTvdbId()."/translations/fra");

            } catch (Exception) {
                $data = null;
            }

            if ($data !== null && $data['status'] === "success") {
                $serie->setName($data['data']['name']);
                $serie->setVfName(true);

                $this->manager->persist($serie);
                $this->manager->flush();
            }
        }

        $episodes = $this->episodeShowRepository->findBySerieWithTVDB();

        foreach ($episodes as $episode) {

            try {

                $data = $this->TVDBService->getData("/episodes/".$episode->getTvdbId()."/translations/fra");

            } catch (Exception) {
                $data = null;
            }

            if ($data !== null && $data['status'] === "success") {
                $episode->setName($data['data']['name']);
                $episode->setVfName(true);

                $this->manager->persist($episode);
                $this->manager->flush();
            }
        }

        $episodes = $this->episodeShowRepository->findByDurationNull();

        foreach ($episodes as $episode) {

            try {

                $data = $this->TVDBService->getData("/episodes/".$episode->getTvdbId());

            } catch (Exception) {
                $data = null;
            }

            if ($data !== null && $data['status'] === "success") {

                $duration = $data['data']['runtime'] * 60000;

                $episode->setDuration($duration);

                $this->manager->persist($episode);
                $this->manager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
