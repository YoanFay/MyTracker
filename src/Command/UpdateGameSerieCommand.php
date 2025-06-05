<?php

namespace App\Command;

use App\Entity\GameSerie;
use App\Repository\GameRepository;
use App\Repository\GameSerieRepository;
use App\Service\IGDBService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-game-serie',
    description: 'Pour mettre à jour la série des jeux',
)]
class UpdateGameSerieCommand extends Command
{


    private ObjectManager $manager;

    private GameRepository $gameRepository;

    private GameSerieRepository $gameSerieRepository;

    private IGDBService $IGDBService;


    public function __construct(GameRepository $gameRepository, GameSerieRepository $gameSerieRepository, IGDBService $IGDBService, ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->gameRepository = $gameRepository;
        $this->gameSerieRepository = $gameSerieRepository;
        $this->apiService = $IGDBService;
        $this->manager = $managerRegistry->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $games = $this->gameRepository->findGameNotSerie();

        foreach ($games as $game) {

            $body = 'fields name,game_modes,genres,themes,version_parent; where id = '.$game->getIgdbId().';';

            $data = $this->apiService->getData('games', $body);

            $id = $data['id'];
            $idParent = null;

            if (isset($data['version_parent'])) {
                $idParent = $data['version_parent'];
            }

            $body = 'fields id,name,games;where games = ['.$id.'];';

            $dataSerie = $this->apiService->getData('collections', $body);

            if (empty($dataSerie) && $idParent) {

                $body = 'fields id,name,games;where games = ['.$idParent.'];';

                $dataSerie = $this->apiService->getData('collections', $body);
                
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

                $this->manager->persist($game);
                $this->manager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
