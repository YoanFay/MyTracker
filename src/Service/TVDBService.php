<?php

namespace App\Service;

use App\Entity\Serie;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TVDBService
{

    public function getData($url){

        $client = new Client();

        $token = self::getKey();

        try {
            $response = $client->get("https://api4.thetvdb.com/v4".$url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            $data = null;
        }

        return $data;
    }


    public function updateSerieName(Serie $serie): void
    {

        $data = self::getData("/series/".$serie->getTvdbId()."/translations/fra");

        if ($data !== null && $data['status'] === "success") {
            $serie->setName($data['data']['name']);
            $serie->setVfName(true);
        }
    }

    public function updateArtwork(Serie $serie, KernelInterface $kernel): void
    {

        $data = self::getData("/series/".$serie->getTvdbId()."/artworks?lang=fra&type=2");

        $status = $data['status'];
        $data = $data['data'];

        if ($status === "success" && $data['artworks'] == []) {

            $data = self::getData("/series/".$serie->getTvdbId()."/artworks?lang=eng&type=2");

            $status = $data['status'];
            $data = $data['data'];
        }

        if ($status === "success" && $data['artworks'] == []) {
            return;
        }

        // Lien de l'image à télécharger
        $lienImage = $data['artworks'][0]['image'];

        $cover = imagecreatefromstring(file_get_contents($lienImage));

        $projectDir = $kernel->getProjectDir();

        // Chemin où enregistrer l'image
        $cheminImageDestination = "/public/image/serie/poster/" . $serie->getSlug().'.jpeg';

        // Téléchargement et enregistrement de l'image
        if (imagejpeg($cover, $projectDir . $cheminImageDestination, 100)) {
            $serie->setArtwork($cheminImageDestination);
        } else {
            $serie->setArtwork(null);
        }
    }


    /**
     * @throws InvalidArgumentException
     */
    public function getKey()
    {

        $cache = new FilesystemAdapter();

        return $cache->get('apiKeyTVDB', function (ItemInterface $item) {

            $item->expiresAfter(2592000);

            $data = self::getData("/login");

            return $data['data']['token'];
        });

    }

}