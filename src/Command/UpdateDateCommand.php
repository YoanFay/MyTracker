<?php

namespace App\Command;

use App\Entity\EpisodeShow;
use App\Entity\Serie;
use App\Repository\EpisodeShowRepository;
use App\Repository\SerieRepository;
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

    private ObjectManager $manager;


    public function __construct(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository, ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->episodeShowRepository = $episodeShowRepository;
        $this->manager = $managerRegistry->getManager();
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

        $apiUrl = 'https://api4.thetvdb.com/v4';

        $apiToken = '8f3a7d8f-c61f-4bf7-930d-65eeab4b26ad';

        $response = $client->post($apiUrl."/login", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => ['apiKey' => $apiToken],
        ]);

        $data = json_decode($response->getBody(), true);

        // Récupérez le token
        $token = $data['data']['token'];

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

        $series = $this->serieRepository->UpdateAired();

        foreach ($series as $serie) {

            $response = $client->get($apiUrl."/series/".$serie->getTvdbId()."/extended?meta=translations&short=true", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $nextAired = DateTime::createFromFormat('Y-m-d', $data['data']['nextAired']);
            $lastAired = DateTime::createFromFormat('Y-m-d', $data['data']['lastAired']);

            $serie->setNextAired($nextAired);
            $serie->setLastAired($lastAired);

            $serie->setStatus($data['data']['status']['name']);

            $this->manager->persist($serie);
            $this->manager->flush();
        }

        return Command::SUCCESS;
    }
}
