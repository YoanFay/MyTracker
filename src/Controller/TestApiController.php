<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestApiController extends AbstractController
{
    /**
     * @throws GuzzleException
     */
    #[Route('/test/api', name: 'app_test_api')]
    public function index(): Response
    {

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { title{english}, type, status, relations{edges{id, relationType}, nodes{id}} ,studios{nodes{id, name, isAnimationStudio}}}}';

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

        $data = json_decode($response->getBody(), true);

        dd($data);


        return $this->render('test_api/index.html.twig', [
            'controller_name' => 'TestApiController',
        ]);
    }
}
