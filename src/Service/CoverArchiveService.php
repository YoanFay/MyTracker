<?php

namespace App\Service;

use App\Entity\Artwork;
use App\Entity\Company;
use App\Entity\Episode;
use App\Entity\Music;
use App\Entity\Serie;
use App\Repository\CompanyRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GdImage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CoverArchiveService
{

    private KernelInterface $kernel;

    private MBService $MBService;


    public function __construct(KernelInterface $kernel, MBService $MBService)
    {

        $this->kernel = $kernel;
        $this->MBService = $MBService;

    }


    public function updateArtwork(Music $music): bool
    {

        $data = self::getData("/release/".$music->getMbid());

        if (isset($data['images'][0])) {
            $image = $data['images'][0]['image'];

            $projectDir = $this->kernel->getProjectDir();

            /** @var string $dataImage */
            $dataImage = file_get_contents($image);

            /** @var GdImage $cover */
            $cover = imagecreatefromstring($dataImage);

            // Chemin où enregistrer l'image
            $cheminImageDestination = "/public/image/music/cover/".$music->getId().'.jpeg';

            // Téléchargement et enregistrement de l'image
            if (imagejpeg($cover, $projectDir.$cheminImageDestination, 100)) {
                return true;
            }
        } else {

            $releaseGroup = $this->MBService->searchReleaseGroup($music->getMbid());

            if (isset($releaseGroup['release-groups'][0])) {

                $data = self::getData("/release-group/".$releaseGroup['release-groups'][0]['id']);

                if (isset($data['images'][0])) {
                    $image = $data['images'][0]['image'];

                    $projectDir = $this->kernel->getProjectDir();

                    /** @var string $dataImage */
                    $dataImage = file_get_contents($image);

                    /** @var GdImage $cover */
                    $cover = imagecreatefromstring($dataImage);

                    // Chemin où enregistrer l'image
                    $cheminImageDestination = "/public/image/music/cover/".$music->getId().'.jpeg';

                    // Téléchargement et enregistrement de l'image
                    if (imagejpeg($cover, $projectDir.$cheminImageDestination, 100)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }


    public function getData(string $url): mixed
    {

        $apiUrl = "https://coverartarchive.org".$url;

        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'MyTracker/1.0 (yoanfayolle.yf@gmail.com)');
        $response = curl_exec($curl);
        curl_close($curl);

        if ($response === false) {
            dd('Erreur cURL : '.curl_error($curl));
        } else {
            return json_decode($response, true);
        }
    }

}