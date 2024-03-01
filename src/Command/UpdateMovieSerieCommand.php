<?php

namespace App\Command;

use App\Entity\GameSerie;
use App\Repository\GameRepository;
use App\Repository\GameSerieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-game-serie',
    description: 'Add a short description for your command',
)]
class UpdateMovieSerieCommand extends Command
{


    private ObjectManager $manager;

    private GameRepository $gameRepository;

    private GameSerieRepository $gameSerieRepository;


    public function __construct(GameRepository $gameRepository, GameSerieRepository $gameSerieRepository,ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->gameRepository = $gameRepository;
        $this->gameSerieRepository = $gameSerieRepository;
        $this->manager = $managerRegistry->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $client = new Client();

        $response = $client->post("https://id.twitch.tv/oauth2/token?client_id=sd5xdt5w2lkjr7ws92fxjdlicvb5u2&client_secret=tymefepntjuva1n9ipa3lkjts2pmdh&grant_type=client_credentials", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        $token = "Bearer ".$data['access_token'];

        $games = $this->gameRepository->findGameNotSerie();

        foreach ($games as $game){

            $response = $client->post("https://api.igdb.com/v4/games", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields name,game_modes,genres,themes; where id = '.$game->getIgdbId().';'
            ]);

            $data = json_decode($response->getBody(), true)[0];

            $id = $data['id'];
            $idParent = null;

            if (isset($data['version_parent'])){
                $idParent = $data['version_parent'];
            }

            $response = $client->post("https://api.igdb.com/v4/collections", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields id,name,games;where games = ['.$id.'];'
            ]);

            $dataSerie = json_decode($response->getBody(), true);

            dump($dataSerie);
            dump(empty($dataSerie));
            dump(empty($dataSerie) && $idParent);

            if (empty($dataSerie) && $idParent){

                $response = $client->post("https://api.igdb.com/v4/collections", [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                        'Authorization' => $token
                    ],
                    'body' => 'fields id,name,games;where games = ['.$idParent.'];'
                ]);

                $dataSerie = json_decode($response->getBody(), true);
            }

            $saveSeries = null;
            $countGamesSeries = 0;

            foreach ($dataSerie as $series) {

                if (count($series['games']) > $countGamesSeries) {
                    $saveSeries = $series;
                }

            }

            if ($saveSeries) {
                $serie = $this->gameSerieRepository->findOneBy(['igdbId' => $saveSeries['id']]);

                if (!$serie) {

                    $serie = new GameSerie();

                    $serie->setName($saveSeries['name']);
                    $serie->setIgdbId($saveSeries['id']);

                    $this->manager->persist($serie);
                    $this->manager->flush();

                }

                $game->setSerie($serie);
            }
        }

        return Command::SUCCESS;
    }
}
