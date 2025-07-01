<?php

namespace App\Command;

use App\Repository\GameRepository;
use App\Service\IGDBService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
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

    private IGDBService $IGDBService;


    public function __construct(GameRepository $gameRepository, IGDBService $IGDBService, ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->gameRepository = $gameRepository;
        $this->IGDBService = $IGDBService;
        $this->manager = $managerRegistry->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $games = $this->gameRepository->findAll();

        foreach ($games as $game) {

            dump($game->getName());

            $body = 'fields name,aggregated_rating,aggregated_rating_count,rating,rating_count; where id = '.$game->getIgdbId().';';

            $data = $this->IGDBService->getData('games', $body)[0];

            if (array_key_exists('aggregated_rating', $data) && $data['aggregated_rating_count'] > 0) {
                $game->setAggregatedRating(round($data['aggregated_rating'], 2));
                $game->setAggregatedRatingCount($data['aggregated_rating_count']);
            }

            if (array_key_exists('rating', $data) && $data['rating_count'] > 0) {
                $game->setRating(round($data['rating'], 2));
                $game->setRatingCount($data['rating_count']);
            }

            $this->manager->persist($game);

        }

        $this->manager->flush();

        return Command::SUCCESS;
    }
}
