<?php

namespace App\Service;

use App\Entity\Serie;
use App\Entity\SerieUpdate;
use App\Repository\SerieUpdateRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

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


    public function updateFirstAiredAnime($anime)
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { startDate{day, month, year} }}';

        $data = $this->aniListService->getData($query, $anime, false);

        $firstDate = $data['startDate']['year']."-".$data['startDate']['month']."-".$data['startDate']['day'];

        $firstAired = DateTime::createFromFormat('Y-m-d', $firstDate);

        $anime->setFirstAired($firstAired);

        $this->manager->persist($anime);
        $this->manager->flush();

    }


    public function updateFirstAired($serie)
    {

        $data = $this->TVDBService->getData("/series/".$serie->getTvdbId()."/extended?meta=translations&short=true");

        $firstAired = DateTime::createFromFormat('Y-m-d', $data['data']['firstAired']);

        $serie->setFirstAired($firstAired);
        $this->manager->persist($serie);
        $this->manager->flush();

    }


    public function updateEndedAnime($anime)
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { status, relations{ edges{relationType}, nodes{title{english}} } }}';

        $data = $this->aniListService->getData($query, $anime);

        if ($data) {
            $status = $this->aniListService->getStatus($data);
        } else {
            return false;
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
        $this->manager->flush();

    }


    public function updateEnded($serie)
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


    public function updateAiredAnime(Serie $anime)
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { nextAiringEpisode{airingAt}, startDate{day, month, year}, endDate{day, month, year}, status, relations{ edges{relationType}, nodes{title{english}} }}}';

        $data = $this->aniListService->getData($query, $anime);

        if ($data) {
            $status = $this->aniListService->getStatus($data);
        } else {
            return false;
        }

        $serieUpdate = $this->serieUpdateRepository->serieDate($anime, $this->today->format('Y-m-d'));

        if (!$serieUpdate) {
            $serieUpdate = new SerieUpdate();
            $serieUpdate->setSerie($anime);
            $serieUpdate->setUpdatedAt($this->today);
        }

        if ($data['nextAiringEpisode']) {

            $nextAired = new DateTime();

            $nextAired->setTimestamp($data['nextAiringEpisode']['airingAt']);

        } else if ($status === "Upcoming" && $data['startDate']['year']) {

            $typeDate = 'year';

            if ($data['startDate']['day']){
                $typeDate = 'day';
            }elseif ($data['startDate']['month']){
                $typeDate = 'month';
            }

            $serieUpdate->setNextAiredType($typeDate);

            $day = $data['startDate']['day'] ?? 1;
            $month = $data['startDate']['month'] ?? 1;
            $year = $data['startDate']['year'];

            $firstDate = $year."-".$month."-".$day;

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
            $serieUpdate->setNextAiredType($nextAired);

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

        $anime->setLastAired($lastAired);

        if ($anime->getStatus() !== $status) {

            $serieUpdate->setOldStatus($anime->getStatus());
            $serieUpdate->setNewStatus($status);
            $anime->setStatus($status);

            $this->manager->persist($serieUpdate);

        }

        $this->manager->persist($anime);
        $this->manager->flush();

    }


    public function updateAired($serie)
    {

        $serieUpdate = $this->serieUpdateRepository->serieDate($serie, $this->today->format('Y-m-d'));

        if (!$serieUpdate) {
            $serieUpdate = new SerieUpdate();
            $serieUpdate->setSerie($serie);
            $serieUpdate->setUpdatedAt($this->today);
        }

        $data = $this->TVDBService->getData("/series/".$serie->getTvdbId()."/extended?meta=translations&short=true");

        if ($data['data']['nextAired']) {

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
        $serie->setLastAired($lastAired);
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