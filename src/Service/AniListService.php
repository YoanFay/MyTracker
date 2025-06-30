<?php

namespace App\Service;

use App\Entity\Serie;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AniListService
{

    private ObjectManager $manager;


    public function __construct(ManagerRegistry $managerRegistry)
    {

        $this->manager = $managerRegistry->getManager();
    }


    public function getData(string $query, Serie $serie, bool $lastSeason = true): mixed
    {

        if ($lastSeason) {
            $name = $this->getLastSeasonName($serie);
        } else {
            $name = $serie->getNameEng();
        }

        dump($name);

        $variables = [
            "search" => mb_convert_kana($name, 'a', 'UTF-8')
        ];

        return $this->request($query, $variables);

    }


    public function getLastSeasonName(Serie $serie): mixed
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { endDate{day, month, year}, status, relations{ edges{relationType}, nodes{title{english}, title{romaji}} }}}';

        if (!$serie->getLastSeasonName()) {
            $serie->setLastSeasonName($serie->getNameEng());
        }

        $name = $serie->getLastSeasonName();

        $ok = true;

        do {

            dump($name);

            $variables = [
                "search" => mb_convert_kana($name, 'a', 'UTF-8')
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

                    if ($name === null) {
                        $name = $data['relations']['nodes'][$relationKey]['title']['romaji'];
                    }

                } else {
                    $ok = false;
                }

                if ($relation) {

                    $serie->setLastSeasonName($name);

                    if ($data['endDate']['year']) {

                        /** @var DateTime $endDate */
                        $endDate = DateTime::createFromFormat('Y-m-d', $data['endDate']['year']."-".$data['endDate']['month']."-".$data['endDate']['day']);

                        $serie->setLastAired($endDate);
                    }

                    $this->manager->persist($serie);
                    $this->manager->flush();

                }
            }

        } while ($ok);

        return $name;

    }


    /**
     * @param string $query
     * @param array<string, mixed>  $variables
     *
     * @return mixed|null
     */
    public function request(string $query, array $variables): mixed
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


    /**
     * @param mixed[] $data
     *
     * @return string
     */
    public function getStatus(array $data): string
    {

        return match ($data['status']) {
            "FINISHED" => "Ended",
            "RELEASING" => "Continuing",
            "NOT_YET_RELEASED" => "Upcoming",
            default => throw new \InvalidArgumentException(sprintf('Le status "%s" n\'est pas supporté.', $data['status'])),
        };

    }


    public function getDataByName(string $query, string $name): mixed
    {

        $variables = [
            "search" => mb_convert_kana($name, 'a', 'UTF-8')
        ];

        return $this->request($query, $variables);

    }


    public function getPrequelSeasonName(string $name): string
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { status, relations{ edges{relationType}, nodes{title{english}} }}}';

        $variables = [
            "search" => mb_convert_kana($name, 'a', 'UTF-8')
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


    public function setScore(Serie $anime): void
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { title{romaji}, stats { scoreDistribution {score, amount}}}}';

        $name = mb_convert_kana($anime->getName(), 'a', 'UTF-8');

        if ($anime->getNameEng()) {
            $name = mb_convert_kana($anime->getNameEng(), 'a', 'UTF-8');
        }

        $score = 0;
        $vote = 0;

        do {

            $ok = true;

            $variables = [
                "search" => $name
            ];

            $data = $this->request($query, $variables);

            if ($data === null) {
                $data = $this->request($query, $variables);


                if ($data === null) {

                    $ok = false;
                }
            }

            if (!$ok) {
                continue;
            }

            foreach ($data['stats']['scoreDistribution'] as $stat) {

                $score += $stat['score'] * $stat['amount'];
                $vote += $stat['amount'];

            }

            $name = $this->getSequel($data['title']['romaji']);

        } while ($name and $ok);

        if ($vote > 0) {

            /** @var ?int $finalScore */
            $finalScore = round($score / $vote, 0, PHP_ROUND_HALF_DOWN);

            $anime->setScore($finalScore);

            $this->manager->persist($anime);
            $this->manager->flush();

            dump($anime->getName().' : '.$finalScore.'%');
        } else {
            dump($anime->getName().' : échec');
        }

    }


    public function getSequel(string $name): ?string
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { relations{edges{relationType}, nodes{title{romaji}}}}}';

        $variables = [
            "search" => mb_convert_kana($name, 'a', 'UTF-8')
        ];

        $data = $this->request($query, $variables);

        if (!$data) {
            return null;
        }

        $relationKey = null;
        foreach ($data['relations']['edges'] as $key => $relationType) {
            if ($relationType['relationType'] === "SEQUEL") {
                $relationKey = $key;
                break;
            }
        }

        if ($relationKey === null) {
            return null;
        }

        return $data['relations']['nodes'][$relationKey]['title']['romaji'];

    }

}