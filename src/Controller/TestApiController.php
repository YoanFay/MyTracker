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

        $query = '
query ($id: Int) { # Define which variables will be used in the query (id)
  Media (id: $id, type: ANIME) { # Insert our variables into the query arguments (id) (type: ANIME is hard-coded in the query)
    id
    title {
      romaji
      english
      native
    }
  }
}
';

// Define our query variables and values that will be used in the query request
        $variables = [
            "id" => 15125
        ];

// Make the HTTP Api request
        $http = new Client;
        $response = $http->post('https://graphql.anilist.co', [
            'json' => [
                'query' => $query,
                'variables' => $variables,
            ]
        ]);

        dd($response);


        return $this->render('test_api/index.html.twig', [
            'controller_name' => 'TestApiController',
        ]);
    }
}
