<?php

namespace App\Command;

use App\Entity\SerieUpdate;
use App\Repository\SerieRepository;
use App\Repository\SerieUpdateRepository;
use App\Service\TVDBService;
use DateTime;
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


class UpdateDateCommand extends Command
{

    private SerieRepository $serieRepository;

    private SerieUpdateRepository $serieUpdateRepository;

    private ObjectManager $manager;

    private TVDBService $TVDBService;


    public function __construct(SerieRepository $serieRepository, SerieUpdateRepository $serieUpdateRepository, ManagerRegistry $managerRegistry, TVDBService $TVDBService)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->serieUpdateRepository = $serieUpdateRepository;
        $this->manager = $managerRegistry->getManager();
        $this->TVDBService = $TVDBService;
    }


    protected function configure(): void
    {

        $this->setName('app:update-date');
        $this->setDescription('Pour les date des épisodes et des séries');
    }


    /**
     * @throws GuzzleException|NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $client = new Client();
        $today = new DateTime();

        $apiUrl = 'https://api4.thetvdb.com/v4';

        // Récupérez le token
        $token = $this->TVDBService->getKey();

        $series = $this->serieRepository->noFirstAired();

        foreach ($series as $serie) {

            $response = $client->get($apiUrl."/series/".$serie->getTvdbId()."/extended?meta=translations&short=true", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $firstAired = DateTime::createFromFormat('Y-m-d', $data['data']['firstAired']);

            $serie->setFirstAired($firstAired);

            $this->manager->persist($serie);
            $this->manager->flush();
        }

        $series = $this->serieRepository->ended();

        foreach ($series as $serie) {

            $response = $client->get($apiUrl."/series/".$serie->getTvdbId()."/extended?meta=translations&short=true", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($serie->getStatus() !== $data['data']['status']['name']) {

                $serieUpdate = $this->serieUpdateRepository->serieDate($serie, $today->format('Y-m-d'));

                if (!$serieUpdate) {
                    $serieUpdate = new SerieUpdate();
                    $serieUpdate->setSerie($serie);
                    $serieUpdate->setUpdatedAt($today);
                }

                $serieUpdate->setOldStatus($serie->getStatus());
                $serieUpdate->setNewStatus($data['data']['status']['name']);
                $serie->setStatus($data['data']['status']['name']);

                $this->manager->persist($serieUpdate);

            }
            $this->manager->persist($serie);
            $this->manager->flush();
        }

        $series = $this->serieRepository->updateAired();

        foreach ($series as $serie) {

            $serieUpdate = $this->serieUpdateRepository->serieDate($serie, $today->format('Y-m-d'));

            if (!$serieUpdate) {
                $serieUpdate = new SerieUpdate();
                $serieUpdate->setSerie($serie);
                $serieUpdate->setUpdatedAt($today);
            }

            $response = $client->get($apiUrl."/series/".$serie->getTvdbId()."/extended?meta=translations&short=true", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

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

        return Command::SUCCESS;
    }
}
