<?php

namespace App\Command;

use App\Entity\EpisodeShow;
use App\Repository\EpisodeShowRepository;
use App\Repository\SerieRepository;
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
use Symfony\Component\HttpKernel\KernelInterface;


class UpdateArtworkCommand extends Command
{

    private SerieRepository $serieRepository;

    private ObjectManager $manager;
    
    private KernelInterface $kernel;


    public function __construct(SerieRepository $serieRepository, ManagerRegistry $managerRegistry, KernelInterface $kernel)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->manager = $managerRegistry->getManager();
        $this->kernel = $kernel;
    }


    protected function configure(): void
    {

        $this->setName('app:update-artwork');
        $this->setDescription('Pour les images des séries');
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

        $series = $this->serieRepository->findArtworkId();

        foreach ($series as $serie) {

            $response = $client->get($apiUrl."/series/".$serie->getTvdbId()."/artworks?lang=fra&type=2", [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $status = $data['status'];
            $data = $data['data'];
            if ($status === "success" && $data['artworks'] == []) {

                $response = $client->get($apiUrl."/series/".$serie->getTvdbId()."/artworks?lang=eng&type=2", [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                ]);

                $data = json_decode($response->getBody(), true);
                $status = $data['status'];
                $data = $data['data'];
            }

            if ($status === "success" && $data['artworks'] == []) {
                continue;
            }
            
            // Lien de l'image à télécharger
            $lienImage = $data['artworks'][0]['image'];

            $cover = null;

            $fileContent = file_get_contents($lienImage);

            if($fileContent !== false){

                $cover = imagecreatefromstring($fileContent);

            }
            
            $projectDir = $this->kernel->getProjectDir();

            // Chemin où enregistrer l'image
            $cheminImageDestination = "/public/image/serie/poster/" . $serie->getSlug().'.jpeg';

            // Téléchargement et enregistrement de l'image
            if ($cover && imagejpeg($cover, $projectDir . $cheminImageDestination, 100)) {
                $serie->setArtwork($cheminImageDestination);
            } else {
                $serie->setArtwork(null);
            }

            $this->manager->persist($serie);
            $this->manager->flush();
        }

        return Command::SUCCESS;
    }
}
