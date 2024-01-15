<?php

namespace App\Command;

use App\Entity\EpisodeShow;
use App\Repository\EpisodeShowRepository;
use App\Repository\SerieRepository;
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

    public function __construct(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository)
    {
        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->episodeShowRepository = $episodeShowRepository;
    }

    protected function configure(): void
    {
        $this->setName('app:update-id');
    }

    /**
     * @throws GuzzleException
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

        foreach ($series as $serie){
            /** @var EpisodeShow $episode */
            $episode = $this->episodeShowRepository->findBySerie($serie);

            $response = $client->get($apiUrl."/episodes/".$episode->getId(), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            dump(json_decode($response->getBody(), true));
        }

        return Command::SUCCESS;
    }
}
