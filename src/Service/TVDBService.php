<?php

namespace App\Service;

use App\Entity\Artwork;
use App\Entity\Company;
use App\Entity\Episode;
use App\Entity\Serie;
use App\Repository\CompanyRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TVDBService
{

    private KernelInterface $kernel;

    private ObjectManager $manager;

    private CompanyRepository $companyRepository;


    public function __construct(KernelInterface $kernel, ManagerRegistry $managerRegistry, CompanyRepository $companyRepository)
    {

        $this->kernel = $kernel;
        $this->manager = $managerRegistry->getManager();
        $this->companyRepository = $companyRepository;
    }


    public function getSerieIdByEpisodeId($episodeId)
    {

        $data = self::getData("/episodes/".$episodeId);

        return $data['data']['seriesId'];

    }


    public function getData($url)
    {

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


    /**
     * @throws InvalidArgumentException
     */
    public function getKey()
    {

        $cache = new FilesystemAdapter();

        return $cache->get('apiKeyTVDB', function (ItemInterface $item) {

            $item->expiresAfter(2592000);

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

            return $data['data']['token'];
        });

    }


    public function updateSerieInfo(Serie $serie): void
    {

        self::updateSerieName($serie);
        self::updateArtwork($serie);

    }


    public function updateSerieName(Serie $serie): void
    {

        $data = self::getData("/series/".$serie->getTvdbId()."/translations/fra");

        if ($data !== null && $data['status'] === "success") {
            $serie->setName($data['data']['name']);
            $serie->setVfName(true);
        }

        $data = self::getData("/series/".$serie->getTvdbId()."/translations/eng");

        if ($data !== null && $data['status'] === "success") {
            $serie->setNameEng($data['data']['name']);
        }
    }


    public function updateArtwork(Serie $serie): void
    {

        $projectDir = $this->kernel->getProjectDir();

        //$data = self::getData("/series/".$serie->getTvdbId()."/artworks?lang=fra&type=2");
        $data = self::getData("/series/".$serie->getTvdbId()."/artworks?type=2");

        $status = $data['status'];
        $data = $data['data'];

        if ($status === "success" && $data['artworks'] == []) {
            return;
        }

        $image = null;
        $score = -1;

        foreach ($data['artworks'] as $artwork) {
            if ($artwork['language'] === "fra" && $artwork['includesText'] && $artwork['score'] >= $score) {
                $image = $artwork;
                    $score = $artwork['score'];
            }
        }

        if ($image === null) {

            $score = -1;

            foreach ($data['artworks'] as $artwork) {
                if ($artwork['language'] === "eng" && $artwork['includesText'] && $artwork['score'] >= $score) {
                    $image = $artwork;
                        $score = $artwork['score'];
                }
            }
        }

        if ($image === null) {

            $score = -1;

            foreach ($data['artworks'] as $artwork) {
                if (/*$artwork['language'] === null && */$artwork['score'] >= $score) {
                        $image = $artwork;
                        $score = $artwork['score'];
                }
            }
        }

        if ($image === null) {
            print_r($serie->getName()." - Pas d'artwork\n");
            return;
        }

        if ($serie->getArtwork()) {
            unlink($projectDir.$serie->getArtwork()->getPath());
            $this->manager->remove($serie->getArtwork());
        }

        $cover = imagecreatefromstring(file_get_contents($image['image']));

        // Chemin où enregistrer l'image
        $cheminImageDestination = "/public/image/serie/poster/".$serie->getSlug().'.jpeg';

        // Téléchargement et enregistrement de l'image
        if (imagejpeg($cover, $projectDir.$cheminImageDestination, 100)) {

            $serieArtwork = new Artwork();

            $serieArtwork->setType('Série');
            $serieArtwork->setSerie($serie);
            $serieArtwork->setApiId($image['id']);
            $serieArtwork->setHeight($image['height']);
            $serieArtwork->setWidth($image['width']);
            $serieArtwork->setLanguage($image['language']);
            $serieArtwork->setPath($cheminImageDestination);
            $serieArtwork->setText($image['includesText']);

            $this->manager->persist($serieArtwork);
            $this->manager->flush();

            $serie->setArtwork($serieArtwork);

        } else {
            print_r($serie->getName()." - Pas d'artwork\n");
            $serie->setArtwork(null);
        }
    }


    public function updateEpisodeName(Episode $episode): void
    {

        $data = self::getData("/episodes/".$episode->getTvdbId()."/translations/fra");

        if ($data !== null && $data['status'] === "success") {
            $episode->setName($data['data']['name']);
            $episode->setVfName(true);
        }
    }


    public function updateEpisodeDuration(Episode $episode): void
    {

        $data = self::getData("/episodes/".$episode->getTvdbId());

        if ($data !== null && $data['status'] === "success") {

            $duration = $data['data']['runtime'] * 60000;

            $episode->setDuration($duration);
        }
    }

    public function createCompany($id): ?Company
    {

        $data = self::getData("/companies/".$id);

        if ($data !== null && $data['status'] === "success") {

            $data = $data['data'];

            $company = new Company();

            $company->setTvdbId($id);
            $company->setName($data['name']);
            $company->setType($data['companyType']['companyTypeName']);
            $company->setCountry($data['country']);

            if(isset($data['activeDate'])){

                $startedDate = DateTime::createFromFormat('Y-m-d', $data['activeDate']);

                if($startedDate) {
                    $company->setStartedAt($startedDate);
                }
            }

            /*if($data['parentCompany']['id']){

                $searchCompany = $this->companyRepository->findOneBy(['tvdbId' => $data['parentCompany']['id']]);

                if(!$searchCompany){
                    $company->setParent(self::createCompany($data['parentCompany']['id']));
                }else{
                    $company->setParent($searchCompany);
                }

            }*/

            $this->manager->persist($company);
            $this->manager->flush();

            return $company;

        }

        return null;

    }

}