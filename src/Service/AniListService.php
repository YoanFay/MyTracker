<?php

namespace App\Service;

use App\Entity\Artwork;
use App\Entity\Company;
use App\Entity\EpisodeShow;
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

class AniListService
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


    public function getData($query, $serie)
    {

        $name = $this->getLastSeasonName($serie);

        $variables = [
            "search" => $name
        ];

        return $this->request($query, $variables);

    }


    public function getLastSeasonName($serie)
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { status, relations{ edges{relationType}, nodes{title{english}} }}}';

        if (!$serie->getLastSeasonName()) {
            $serie->setLastSeasonName($serie->getNameEng());
        }

        $name = $serie->getLastSeasonName();

        $ok = true;

        do {

            dump($name);

            $variables = [
                "search" => $name
            ];

            $data = $this->request($query, $variables);

            if ($data) {
                $status = $this->getStatus($data);

                $relation = null;
                $relationKey = null;

                foreach ($data['relations']['edges'] as $key => $relationType) {
                    if ($relationType['relationType'] === "SEQUEL") {
                        $relation = $relationType['relationType'];
                        $relationKey = $key;
                    }
                }

                if ($relation) {

                    $serie->setLastSeasonName($name);

                    $this->manager->persist($serie);
                    $this->manager->flush();

                }

                if ($relation && ($status === "Ended" || $status === "Upcoming")) {
                    $name = $data['relations']['nodes'][$relationKey]['title']['english'];
                } else {
                    $ok = false;
                }
            }

        } while ($ok);

        return $name;

    }


    public function request($query, $variables)
    {

        $http = new Client();

        try {
            $response = $http->post('https://graphql.anilist.co', [
                'json' => [
                    'query' => $query,
                    'variables' => $variables,
                ]
            ]);


        } catch (\Exception|GuzzleException $e) {
            sleep(60);
            return null;
        }

        if ($response->getHeader('X-RateLimit-Remaining')[0] == 0) {
            sleep(60);
        }

        $data = json_decode($response->getBody(), true);

        return $data['data']['Media'];

    }


    public function getStatus($data)
    {

        return match ($data['status']) {
            "FINISHED" => "Ended",
            "RELEASING" => "Continuing",
            "NOT_YET_RELEASED" => "Upcoming",
        };

    }

}