<?php

namespace App\Command;

use App\Entity\GamePlatform;
use App\Repository\GamePlatformRepository;
use App\Service\FileService;
use App\Service\StrSpecialCharsLower;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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


    public function __construct(GamePlatformRepository $gamePlatformRepository, StrSpecialCharsLower $strSpecialCharsLower, ManagerRegistry $managerRegistry, FileService $fileService)
    {

        parent::__construct();
        $this->gamePlatformRepository = $gamePlatformRepository;
        $this->strSpecialCharsLower = $strSpecialCharsLower;
        $this->manager = $managerRegistry->getManager();
        $this->fileService = $fileService;
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

        $platforms = $this->gamePlatformRepository->findNoLogo();

        /** @var GamePlatform $platform */
        foreach ($platforms as $platform) {

            $response = $client->post("https://api.igdb.com/v4/platforms", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields versions; where id = '.$platform->getIgdbId().';'
            ]);

            $data = json_decode($response->getBody(), true)[0];

            $version = min($data['versions']);

            $response = $client->post("https://api.igdb.com/v4/platform_versions", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                    'Authorization' => $token
                ],
                'body' => 'fields platform_logo; where id = '.$version.';'
            ]);

            $data = json_decode($response->getBody(), true)[0];

            if (array_key_exists('platform_logo', $data)) {
                $response = $client->post("https://api.igdb.com/v4/platform_logos", [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                        'Authorization' => $token
                    ],
                    'body' => 'fields image_id; where id = '.$data['platform_logo'].';'
                ]);

                $data = json_decode($response->getBody(), true)[0];

                $link = "https://images.igdb.com/igdb/image/upload/t_logo_med/".$data['image_id'].".png";

                $destinationFolder = "/public/image/game/platform/".$this->strSpecialCharsLower->serie($platform->getName()).'.png';

                $platform->setLogo($this->fileService->addFile($link, $destinationFolder));

            }

            $this->manager->persist($platform);

        }

        $this->manager->flush();

        return Command::SUCCESS;
    }
}
