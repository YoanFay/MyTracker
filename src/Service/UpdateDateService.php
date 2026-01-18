<?php

namespace App\Service;

use App\Entity\Serie;
use App\Entity\SerieUpdate;
use App\Repository\SerieUpdateRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;

class UpdateDateService
{

    private ObjectManager $manager;

    private AniListService $aniListService;

    private TVDBService $TVDBService;

    private DateTime $today;

    private SerieUpdateRepository $serieUpdateRepository;


    public function __construct(ManagerRegistry $managerRegistry, AniListService $aniListService, TVDBService $TVDBService, SerieUpdateRepository $serieUpdateRepository)
    {

        $this->manager = $managerRegistry->getManager();
        $this->aniListService = $aniListService;
        $this->TVDBService = $TVDBService;
        $this->serieUpdateRepository = $serieUpdateRepository;
        $this->today = new DateTime();


    }


    public function updateFirstAiredAnime(Serie $anime): void
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { startDate{day, month, year} }}';

        $data = $this->aniListService->getData($query, $anime, false);

        $firstDate = $data['startDate']['year']."-".$data['startDate']['month']."-".$data['startDate']['day'];

        /** @var DateTime $firstAired */
        $firstAired = DateTime::createFromFormat('Y-m-d', $firstDate);

        $anime->setFirstAired($firstAired);

        $this->manager->persist($anime);
        $this->manager->flush();

    }


    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function updateFirstAired(Serie $serie): void
    {

        $data = $this->TVDBService->getData("/series/".$serie->getTvdbId()."/extended?meta=translations&short=true");

        /** @var DateTime $firstAired */
        $firstAired = DateTime::createFromFormat('Y-m-d', $data['data']['firstAired']);

        $serie->setFirstAired($firstAired);
        $this->manager->persist($serie);
        $this->manager->flush();

    }


    public function updateLastAiredAnime(Serie $anime): void
    {


        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { endDate{day, month, year} }}';

        if ($anime->getLastSeasonName()) {

            $data = $this->aniListService->getDataByName($query, $anime->getLastSeasonName());

            if (!$data['endDate']['year']) {
                $data = $this->aniListService->getDataByName($query, $this->aniListService->getPrequelSeasonName($anime->getLastSeasonName()));
            }

        } elseif ($anime->getNameEng()) {
            $data = $this->aniListService->getDataByName($query, $anime->getNameEng());
        }else{
            return;
        }

        if (!$data['endDate']['year']) {
            return;
        }

        $lastDate = $data['endDate']['year']."-".$data['endDate']['month']."-".$data['endDate']['day'];

        /** @var DateTime $lastAired */
        $lastAired = DateTime::createFromFormat('Y-m-d', $lastDate);

        $anime->setLastAired($lastAired);

        $this->manager->persist($anime);
        $this->manager->flush();

    }


    /**
     * @throws NonUniqueResultException
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function updateEnded(Serie $serie): void
    {

        $data = $this->TVDBService->getData("/series/".$serie->getTvdbId()."/extended?meta=translations&short=true");

        if ($serie->getStatus() !== $data['data']['status']['name']) {

            $serieUpdate = $this->serieUpdateRepository->serieDate($serie, $this->today->format('Y-m-d'));
            if (!$serieUpdate) {
                $serieUpdate = new SerieUpdate();
                $serieUpdate->setSerie($serie);
                $serieUpdate->setUpdatedAt($this->today);
            }

            $serieUpdate->setOldStatus($serie->getStatus());
            $serieUpdate->setNewStatus($data['data']['status']['name']);
            $serie->setStatus($data['data']['status']['name']);
            $this->manager->persist($serieUpdate);
        }

        $this->manager->persist($serie);
        $this->manager->flush();

    }


    public function updateAiredAnime(Serie $anime): void
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { nextAiringEpisode{airingAt}, startDate{day, month, year}, endDate{day, month, year}, status, relations{ edges{relationType}, nodes{title{english}} }}}';

        $data = $this->aniListService->getData($query, $anime);

        if ($data) {
            $status = $this->aniListService->getStatus($data);
        } else {
            return;
        }

        $serieUpdate = $this->serieUpdateRepository->serieDate($anime, $this->today->format('Y-m-d'));

        if (!$serieUpdate) {
            $serieUpdate = new SerieUpdate();
            $serieUpdate->setSerie($anime);
            $serieUpdate->setUpdatedAt($this->today);
        }

        $typeDate = null;

        if ($data['nextAiringEpisode']) {

            $nextAired = new DateTime();

            $nextAired->setTimestamp($data['nextAiringEpisode']['airingAt']);

        } else if ($status === "Upcoming" && $data['startDate']['year']) {

            $typeDate = 'year';

            if ($data['startDate']['day']) {
                $typeDate = 'day';
            } else if ($data['startDate']['month']) {
                $typeDate = 'month';
            }

            $day = $data['startDate']['day'] ?? 1;
            $month = $data['startDate']['month'] ?? 1;
            $year = $data['startDate']['year'];

            $firstDate = $year."-".$month."-".$day;

            /** @var DateTime $nextAired */
            $nextAired = DateTime::createFromFormat('Y-m-d', $firstDate);

        } else {
            $nextAired = null;
        }

        if (
            ($anime->getNextAired() === null && $nextAired !== null) ||
            ($anime->getNextAired() !== null && $nextAired === null) ||
            ($anime->getNextAired() !== null && $nextAired !== null && $anime->getNextAired()->format('Y-m-d') !== $nextAired->format('Y-m-d'))
        ) {
            $serieUpdate->setOldNextAired($anime->getNextAired());
            $serieUpdate->setNewNextAired($nextAired);
            $anime->setNextAired($nextAired);

            $serieUpdate->setOldAiredType($serieUpdate->getNextAiredType());
            $serieUpdate->setNextAiredType($typeDate);
            $anime->setNextAiredType($typeDate);

            $this->manager->persist($serieUpdate);
        }


        if ($data['endDate']['year']) {

            $day = $data['endDate']['day'] ?? 1;
            $month = $data['endDate']['month'] ?? 1;
            $year = $data['endDate']['year'];

            $lastDate = $year."-".$month."-".$day;

            /** @var DateTime $lastAired */
            $lastAired = DateTime::createFromFormat('Y-m-d', $lastDate);

        } else {
            $lastAired = null;
        }

        if ($lastAired) {
            $anime->setLastAired($lastAired);
        }

        if ($anime->getStatus() !== $status) {

            $serieUpdate->setOldStatus($anime->getStatus());
            $serieUpdate->setNewStatus($status);
            $anime->setStatus($status);

            $this->updateEndedAnime($anime);

            $this->manager->persist($serieUpdate);

        }

        $this->manager->persist($anime);
        $this->manager->flush();

    }


    public function updateEndedAnime(Serie $anime, $last = false): void
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { status, relations{ edges{relationType}, nodes{title{english}} } }}';

        $data = $this->aniListService->getData($query, $anime);

        if ($data) {
            $status = $this->aniListService->getStatus($data);
        } else {
            return;
        }

        if ($anime->getStatus() !== $status) {

            $serieUpdate = $this->serieUpdateRepository->serieDate($anime, $this->today->format('Y-m-d'));

            if (!$serieUpdate) {
                $serieUpdate = new SerieUpdate();
                $serieUpdate->setSerie($anime);
                $serieUpdate->setUpdatedAt($this->today);
            }

            $serieUpdate->setOldStatus($anime->getStatus());
            $serieUpdate->setNewStatus($status);
            $anime->setStatus($status);

            $this->manager->persist($serieUpdate);

        }
        $this->manager->persist($anime);

        if ($last){
            $this->manager->flush();
        }

    }


    public function updateAired(Serie $serie): void
    {

        $serieUpdate = $this->serieUpdateRepository->serieDate($serie, $this->today->format('Y-m-d'));

        if (!$serieUpdate) {
            $serieUpdate = new SerieUpdate();
            $serieUpdate->setSerie($serie);
            $serieUpdate->setUpdatedAt($this->today);
        }

        $data = $this->TVDBService->getData("/series/".$serie->getTvdbId()."/extended?meta=translations&short=true");

        if ($data['data']['nextAired']) {

            /** @var DateTime $nextAired */
            $nextAired = DateTime::createFromFormat('Y-m-d H:i', $data['data']['nextAired']." 00:00");
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
        if ($data['data']['lastAired']) {
            $lastAired = DateTime::createFromFormat('Y-m-d', $data['data']['lastAired']);
        } else {
            $lastAired = null;
        }

        if ($lastAired) {
            $serie->setLastAired($lastAired);
        }

        if ($serie->getStatus() !== $data['data']['status']['name']) {
            $serieUpdate->setOldStatus($serie->getStatus());
            $serieUpdate->setNewStatus($data['data']['status']['name']);
            $serie->setStatus($data['data']['status']['name']);
            $this->manager->persist($serieUpdate);
        }
        $this->manager->persist($serie);
        $this->manager->flush();

    }

}