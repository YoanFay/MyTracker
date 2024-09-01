<?php

namespace App\Command;

use App\Entity\SerieUpdate;
use App\Repository\SerieRepository;
use App\Repository\SerieUpdateRepository;
use App\Service\AniListService;
use App\Service\TVDBService;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateDateCommand extends Command
{

    private SerieRepository $serieRepository;

    private SerieUpdateRepository $serieUpdateRepository;

    private ObjectManager $manager;

    private TVDBService $TVDBService;

    private AniListService $aniListService;


    public function __construct(SerieRepository $serieRepository, SerieUpdateRepository $serieUpdateRepository, ManagerRegistry $managerRegistry, TVDBService $TVDBService, AniListService $aniListService)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->serieUpdateRepository = $serieUpdateRepository;
        $this->manager = $managerRegistry->getManager();
        $this->TVDBService = $TVDBService;
        $this->aniListService = $aniListService;
    }


    protected function configure(): void
    {

        $this->setName('app:update-date');
        $this->setDescription('Pour les date des épisodes et des séries');
    }


    /**
     * @throws NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $today = new DateTime();

        $series = $this->serieRepository->noFirstAiredAnime();

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { startDate{day, month, year} }}';

        foreach ($series as $serie) {

            $data = $this->aniListService->getData($query, $serie, false);

            $firstDate = $data['startDate']['year']."-".$data['startDate']['month']."-".$data['startDate']['day'];

            $firstAired = DateTime::createFromFormat('Y-m-d', $firstDate);

            $serie->setFirstAired($firstAired);

            $this->manager->persist($serie);
            $this->manager->flush();
        }

        $series = $this->serieRepository->endedAnime();

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { status, relations{ edges{relationType}, nodes{title{english}} } }}';

        foreach ($series as $serie) {

            $data = $this->aniListService->getData($query, $serie);

            if ($data) {
                $status = $this->aniListService->getStatus($data);
            } else {
                continue;
            }

            if ($serie->getStatus() !== $status) {

                $serieUpdate = $this->serieUpdateRepository->serieDate($serie, $today->format('Y-m-d'));

                if (!$serieUpdate) {
                    $serieUpdate = new SerieUpdate();
                    $serieUpdate->setSerie($serie);
                    $serieUpdate->setUpdatedAt($today);
                }

                $serieUpdate->setOldStatus($serie->getStatus());
                $serieUpdate->setNewStatus($status);
                $serie->setStatus($status);

                $this->manager->persist($serieUpdate);

            }
            $this->manager->persist($serie);
            $this->manager->flush();
        }

        $series = $this->serieRepository->updateAired();

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { nextAiringEpisode{airingAt}, startDate{day, month, year}, endDate{day, month, year}, status, relations{ edges{relationType}, nodes{title{english}} }}}';

        foreach ($series as $serie) {

            $data = $this->aniListService->getData($query, $serie);

            if ($data) {
                $status = $this->aniListService->getStatus($data);
            } else {
                continue;
            }

            $serieUpdate = $this->serieUpdateRepository->serieDate($serie, $today->format('Y-m-d'));

            if (!$serieUpdate) {
                $serieUpdate = new SerieUpdate();
                $serieUpdate->setSerie($serie);
                $serieUpdate->setUpdatedAt($today);
            }

            if ($data['nextAiringEpisode']) {

                $nextAired = new DateTime();

                $nextAired->setTimestamp($data['nextAiringEpisode']['airingAt']);

            } else if ($status === "Upcoming" && $data['startDate']['year']) {

                $day = $data['startDate']['day'] ?? 1;
                $month = $data['startDate']['month'] ?? 1;
                $year = $data['startDate']['year'];

                $firstDate = $year."-".$month."-".$day;

                $nextAired = DateTime::createFromFormat('Y-m-d', $firstDate);

            } else {
                $nextAired = null;
            }

            if (
                ($serie->getNextAired() === null && $nextAired !== null) ||
                ($serie->getNextAired() !== null && $nextAired === null) ||
                ($serie->getNextAired() !== null && $nextAired !== null && $serie->getNextAired()->format('Y-m-d') !== $nextAired->format('Y-m-d'))
            ) {
                $serieUpdate->setOldNextAired($serie->getNextAired());
                $serieUpdate->setNewNextAired($nextAired);
                $serie->setNextAired($nextAired);

                $this->manager->persist($serieUpdate);
            }


            if ($data['endDate']['year']) {

                $day = $data['endDate']['day'] ?? 1;
                $month = $data['endDate']['month'] ?? 1;
                $year = $data['endDate']['year'];

                $lastDate = $year."-".$month."-".$day;

                $lastAired = DateTime::createFromFormat('Y-m-d', $lastDate);

            } else {
                $lastAired = null;
            }

            $serie->setLastAired($lastAired);

            if ($serie->getStatus() !== $status) {

                $serieUpdate->setOldStatus($serie->getStatus());
                $serieUpdate->setNewStatus($status);
                $serie->setStatus($status);

                $this->manager->persist($serieUpdate);

            }

            $this->manager->persist($serie);
            $this->manager->flush();
        }

        return Command::SUCCESS;
    }
}
