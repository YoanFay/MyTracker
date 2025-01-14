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

class AniListService
{

    private ObjectManager $manager;


    public function __construct(ManagerRegistry $managerRegistry)
    {

        $this->manager = $managerRegistry->getManager();
    }


    public function getData($query, Serie $serie, $lastSeason = true)
    {

        if ($lastSeason){
            $name = $this->getLastSeasonName($serie);
        }else{
            $name = $serie->getNameEng();
        }

        $name = mb_convert_kana($name, 'a', 'UTF-8');

        dump($name);

        $variables = [
            "search" => $name
        ];

        return $this->request($query, $variables);

    }


    public function getDataByName($query, $name)
    {

        $variables = [
            "search" => $name
        ];

        return $this->request($query, $variables);

    }


    public function getPrequelSeasonName($name)
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { status, relations{ edges{relationType}, nodes{title{english}} }}}';

            $variables = [
                "search" => $name
            ];

            $data = $this->request($query, $variables);

            if ($data) {

                $relationKey = null;

                foreach ($data['relations']['edges'] as $key => $relationType) {
                    if ($relationType['relationType'] === "PREQUEL") {
                        $relationKey = $key;
                    }
                }

                if ($relationKey !== null) {

                    return $data['relations']['nodes'][$relationKey]['title']['english'];

                }
            }

        return $name;

    }


    public function getLastSeasonName($serie)
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { endDate{day, month, year}, status, relations{ edges{relationType}, nodes{title{english}, title{romaji}} }}}';

        if (!$serie->getLastSeasonName()) {
            $serie->setLastSeasonName($serie->getNameEng());
        }

        $name = $serie->getLastSeasonName();

        $ok = true;

        do {

            $name = mb_convert_kana($name, 'a', 'UTF-8');

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

                if ($relation && ($status === "Ended" || $status === "Upcoming")) {
                    $name = $data['relations']['nodes'][$relationKey]['title']['english'];

                    if ($name === null){
                        $name = $data['relations']['nodes'][$relationKey]['title']['romaji'];
                    }

                } else {
                    $ok = false;
                }

                if ($relation) {

                    $serie->setLastSeasonName($name);

                    if ($data['endDate']['year']){
                        $serie->setLastAired(DateTime::createFromFormat('Y-m-d', $data['endDate']['year']."-".$data['endDate']['month']."-".$data['endDate']['day']));
                    }

                    $this->manager->persist($serie);
                    $this->manager->flush();

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


    public function getSequel($name)
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { relations{edges{relationType}, nodes{title{romaji}}}}}';

        $variables = [
            "search" => $name
        ];

        $data = $this->request($query, $variables);

        if(!$data){
            return null;
        }

        $relationKey = null;
        foreach ($data['relations']['edges'] as $key => $relationType) {
            if ($relationType['relationType'] === "SEQUEL") {
                $relationKey = $key;
                break;
            }
        }

        if($relationKey === null){
            return null;
        }

        return $data['relations']['nodes'][$relationKey]['title']['romaji'];

    }


    public function setScore(Serie $anime): void
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { title{romaji}, stats { scoreDistribution {score, amount}}}}';

        $name = mb_convert_kana($anime->getName(), 'a', 'UTF-8');

        if ($anime->getNameEng()){
            $name = mb_convert_kana($anime->getNameEng(), 'a', 'UTF-8');
        }

        dump($name);

        $score = 0;
        $vote = 0;

        do {

            $ok = true;

            $variables = [
                "search" => $name
            ];

            $data = $this->request($query, $variables);

            if ($data === null){
                $data = $this->request($query, $variables);


                if ($data === null){

                    $ok = false;
                }
            }

            if (!$ok){
                continue;
            }

            foreach ($data['stats']['scoreDistribution'] as $stat) {

                $score += $stat['score'] * $stat['amount'];
                $vote += $stat['amount'];

            }

            $name = $this->getSequel($data['title']['romaji']);

        }while($name and $ok);

        if ($vote > 0) {
            $finalScore = round($score / $vote, 0, PHP_ROUND_HALF_DOWN);

            dump($anime->getName().' : '.$finalScore.'%');
        }else{
            dump($anime->getName().' : échec');
        }

    }

}