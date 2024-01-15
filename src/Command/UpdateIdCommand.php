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


class UpdateIdCommand extends Command
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

        $this->setName('app:update-id');
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

        $series = $this->serieRepository->findNotTvdbId();

        foreach ($series as $serie) {
            $data = null;
            $episode = null;
            /** @var EpisodeShow $episode */
            $episode = $this->episodeShowRepository->findBySerie($serie);

            if ($episode) {
                $response = $client->get($apiUrl."/episodes/".$episode->getTvdbId(), [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                ]);

                $data = json_decode($response->getBody(), true);

                $serie->setTvdbId($data['data']['seriesId']);

                $this->manager->persist($serie);
                $this->manager->flush();
            }

            $episodes = $this->episodeShowRepository->findBySerieWitoutTVDB($serie);

            if ($episodes) {
                foreach ($episodes as $oneEpisode) {

                    if ($oneEpisode->getSerie()->getTvdbId()) {
                        $response = $client->get($apiUrl."/series/".$oneEpisode->getSerie()->getTvdbId()."/episodes/default?page=1&season=".$oneEpisode->getSaisonNumber()."&episodeNumber=".$oneEpisode->getEpisodeNumber(), [
                            'headers' => [
                                'Authorization' => 'Bearer '.$token,
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                            ],
                        ]);

                        $data2 = json_decode($response->getBody(), true);

                        $oneEpisode->setTvdbId($data2['data']['episode'][0]['id']);

                        $this->manager->persist($oneEpisode);
                        $this->manager->flush();
                    }

                }
            }
        }

        return Command::SUCCESS;
    }
}
