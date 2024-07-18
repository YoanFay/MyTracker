<?php

namespace App\Controller;

use App\Repository\SerieRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestApiController extends AbstractController
{
    /**
     * @throws GuzzleException
     */
    #[Route('/test/api', name: 'app_test_api')]
    public function index(SerieRepository $serieRepository): Response
    {

        $series = $serieRepository->findAnimeWithLimit(10);

        $animes = [];

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { title{english}, type, status ,studios{nodes{id, name, isAnimationStudio}}}}';

        foreach ($series as $serie) {

// Define our query variables and values that will be used in the query request
            $variables = [
                "search" => "Alya Sometimes Hides Her Feelings in Russian"
            ];

// Make the HTTP Api request
            $http = new Client;

                $response = $http->post('https://graphql.anilist.co', [
                    'json' => [
                        'query' => $query,
                        'variables' => $variables,
                    ]
                ]);

                if ($response->getHeader('X-RateLimit-Remaining')[0] == 0){
                    sleep($response->getHeader('X-RateLimit-Reset')[0]);
                }

                $data = json_decode($response->getBody(), true);

                $data = $data['data']['Media'];

                $studio = null;

                foreach ($data['studios']['node'] as $node) {
                    if ($node['isAnimationStudio']) {
                        $studio = $node['name'];
                    }
                }

                $animeData = [
                    'name' => $data['title']['english'],
                    'status' => $data['status'],
                    'studio' => $studio,
                ];

                $animes[] = $animeData;

            sleep(30);

        }

        return $this->render('test_api/index.html.twig', [
            'controller_name' => 'TestApiController',
            'animes' => $animes,
        ]);
    }
}
