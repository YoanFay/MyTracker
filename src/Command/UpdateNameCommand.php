<?php

namespace App\Command;

use App\Entity\EpisodeShow;
use App\Repository\EpisodeShowRepository;
use App\Repository\SerieRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class UpdateNameCommand extends Command
{

    private $serieRepository;

    private $episodeShowRepository;

    private $manager;


    public function __construct(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository, ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->episodeShowRepository = $episodeShowRepository;
        $this->manager = $managerRegistry->getManager();
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

        $series = $this->serieRepository->findTvdbId();

        foreach ($series as $serie) {
            
            try{

            $response = $client->get($apiUrl."/series/".$serie->getTvdbId()."/translations/fra", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            }catch(\Exception $e){
                $data = null;
            }

            if ($data !== null && $data['status'] === "success"){
                $serie->setName($data['data']['name']);
                $serie->setVfName(true);

                $this->manager->persist($serie);
                $this->manager->flush();
            }
        }

        $episodes = $this->episodeShowRepository->findBySerieWithTVDB();

        foreach ($episodes as $episode) {
            
            try{

            $response = $client->get($apiUrl."/episodes/".$episode->getTvdbId()."/translations/fra", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            }catch(\Exception $e){
                $data = null;
            }

            if ($data !== null && $data['status'] === "success"){
                $episode->setName($data['data']['name']);
                $episode->setVfName(true);

                $this->manager->persist($episode);
                $this->manager->flush();
            }
        }

        $episodes = $this->episodeShowRepository->findByDurationNull();

        foreach ($episodes as $episode) {

            try{

                $response = $client->get($apiUrl."/episodes/".$episode->getTvdbId(), [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                ]);

                $data = json_decode($response->getBody(), true);

            }catch(\Exception $e){
                $data = null;
            }

            if ($data !== null && $data['status'] === "success"){

                $duration = $data['data']['runtime'] * 60000;

                $episode->setDuration($duration);

                $this->manager->persist($episode);
                $this->manager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
