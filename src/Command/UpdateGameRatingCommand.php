<?php

namespace App\Command;

use App\Entity\GameSerie;
use App\Repository\GameRepository;
use App\Repository\GameSerieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-game-rating',
    description: 'Pour mettre à jour la série des jeux',
)]
class UpdateGameRatingCommand extends Command
{


    private ObjectManager $manager;

    private GameRepository $gameRepository;


    public function __construct(GameRepository $gameRepository, GameSerieRepository $gameSerieRepository, ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->gameRepository = $gameRepository;
        $this->gameSerieRepository = $gameSerieRepository;
        $this->manager = $managerRegistry->getManager();
    }


    /**
     * @throws GuzzleException
     */
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

        foreach ($games as $game) {

            $response = $client->post("https://api.igdb.com/v4/games", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields name,aggregated_rating,aggregated_rating_count,rating,rating_count; where id = '.$game->getIgdbId().';'
            ]);

            $data = json_decode($response->getBody(), true)[0];

            $game->setRating(round($data['rating'], 2));
            $game->setAggregatedRating(round($data['aggregated_rating'], 2));

            $game->setRatingCount($data['rating_count']);
            $game->setAggregatedRatingCount($data['aggregated_rating_count']);

            $this->manager->persist($game);

        }

        $this->manager->flush();

        return Command::SUCCESS;
    }
}
