<?php

namespace App\Command;

use App\Entity\GamePlatform;
use App\Repository\GamePlatformRepository;
use App\Service\IGDBService;
use App\Service\FileService;
use App\Service\StrSpecialCharsLower;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-game-platform-logo',
    description: 'Pour mettre à jour le logo des plateformes',
)]
class UpdateGamePlatformLogoCommand extends Command
{


    private ObjectManager $manager;

    private GamePlatformRepository $gamePlatformRepository;

    private StrSpecialCharsLower $strSpecialCharsLower;

    private FileService $fileService;

    private IGDBService $IGDBService;


    public function __construct(GamePlatformRepository $gamePlatformRepository, StrSpecialCharsLower $strSpecialCharsLower, ManagerRegistry $managerRegistry, FileService $fileService, IGDBService $IGDBService)
    {

        parent::__construct();
        $this->gamePlatformRepository = $gamePlatformRepository;
        $this->strSpecialCharsLower = $strSpecialCharsLower;
        $this->manager = $managerRegistry->getManager();
        $this->fileService = $fileService;
        $this->IGDBService = $IGDBService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $platforms = $this->gamePlatformRepository->findNoLogo();

        foreach ($platforms as $platform) {

            $body = 'fields versions; where id = '.$platform->getIgdbId().';';

            $data = $this->IGDBService->getData('platforms', $body);

            $version = min($data['versions']);

            $body = 'fields platform_logo; where id = '.$version.';';

            $data = $this->IGDBService->getData('platform_versions', $body);

            if (array_key_exists('platform_logo', $data)) {

                $body = 'fields image_id; where id = '.$data['platform_logo'].';';

                $data = $this->IGDBService->getData('platform_logos', $body);

                $link = "https://images.igdb.com/igdb/image/upload/t_logo_med/".$data['image_id'].".png";

                $name = $platform->getName();

                $destinationFolder = "/public/image/game/platform/".$this->strSpecialCharsLower->serie($name).'.png';

                $platform->setLogo($this->fileService->addFile($link, $destinationFolder));

            }

            $this->manager->persist($platform);

        }

        $this->manager->flush();

        return Command::SUCCESS;
    }
}
