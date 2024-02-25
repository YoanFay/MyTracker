<?php

namespace App\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;

class MovieController extends AbstractController
{
    #[Route('/movie', name: 'movie')]
    public function index(MovieRepository $movieRepository): Response
    {

        $movies = $movieRepository->findAll();

        $moviesByDate = [];
        $dateKeys = [];

        foreach ($movies as $movie) {
            $dateKey = $movie->getShowDate()->format("Y-m-d");

            if (!isset($moviesByDate[$dateKey])) {
                $moviesByDate[$dateKey] = [$movie];
                $dateKeys[] = $dateKey;
            } else {
                $moviesByDate[$dateKey][] = $movie;
            }
        }

        return $this->render('movie/index.html.twig', [
            'moviesByDate' => $moviesByDate,
            'dateKeys' => $dateKeys,
            'controller_name' => 'MovieController',
            'navLinkId' => 'movie',
        ]);
    }


    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[Route('/movie/test', name: 'movie_test')]
    public function test(): Response
    {

        $test = '{
  "event": "media.play",
  "user": true,
  "owner": true,
  "Account": {
    "id": 94267393,
    "thumb": "https://plex.tv/users/7bde893376eeecef/avatar?c=1708886802",
    "title": "yoan.f8"
  },
  "Server": {
    "title": "PC-PORTABLE",
    "uuid": "4b87b0f5ee15c68369e4257697e658810bbfe062"
  },
  "Player": {
    "local": true,
    "publicAddress": "88.160.190.207",
    "title": "Chrome",
    "uuid": "xjrphbgzgos8pmgfs3byr94q"
  },
  "Metadata": {
    "librarySectionType": "movie",
    "ratingKey": "31037",
    "key": "/library/metadata/31037",
    "guid": "plex://movie/5d776b95fb0d55001f56c2c7",
    "studio": "Lucasfilm Ltd.",
    "type": "movie",
    "title": "Indiana Jones et le Cadran de la destinée",
    "titleSort": "Indiana Jones et le Cadran de la destinee",
    "librarySectionTitle": "Quasinas Films Chat",
    "librarySectionID": 5,
    "librarySectionKey": "/library/sections/5",
    "originalTitle": "Indiana Jones and the Dial of Destiny",
    "contentRating": "PG-13",
    "summary": "1969. Après avoir passé plus de dix ans à enseigner au Hunter College de New York, l\'estimé docteur Jones, professeur d\'archéologie, est sur le point de prendre sa retraite et de couler des jours paisibles. Tout bascule après la visite surprise de sa filleule Helena Shaw, qui est à la recherche d\'un artefact rare que son père a confié à Indy des années auparavant : le fameux cadran d\'Archimède, une relique qui aurait le pouvoir de localiser les fissures temporelles. En arnaqueuse accomplie, Helena vole l’objet et quitte précipitamment le pays afin de le vendre au plus offrant. Indy n\'a d\'autre choix que de se lancer à sa poursuite. Il ressort son fédora et son blouson de cuir pour une dernière virée...",
    "rating": 7.0,
    "audienceRating": 8.8,
    "year": 2023,
    "tagline": "Une légende affrontera son destin.",
    "thumb": "/library/metadata/31037/thumb/1706838101",
    "art": "/library/metadata/31037/art/1706838101",
    "duration": 9300000,
    "originallyAvailableAt": "2023-06-28",
    "addedAt": 1704114463,
    "updatedAt": 1706838101,
    "audienceRatingImage": "rottentomatoes://image.rating.upright",
    "chapterSource": "media",
    "primaryExtraKey": "/library/metadata/31080",
    "ratingImage": "rottentomatoes://image.rating.ripe",
    "Genre": [
      {
        "id": 114,
        "filter": "genre=114",
        "tag": "Action",
        "count": 90
      },
      {
        "id": 100,
        "filter": "genre=100",
        "tag": "Adventure",
        "count": 108
      },
      {
        "id": 569,
        "filter": "genre=569",
        "tag": "Science-Fiction",
        "count": 68
      }
    ],
    "Country": [
      {
        "id": 5118,
        "filter": "country=5118",
        "tag": "United States of America",
        "count": 82
      }
    ],
    "Guid": [
      {
        "id": "imdb://tt1462764"
      },
      {
        "id": "tmdb://335977"
      },
      {
        "id": "tvdb://134846"
      }
    ],
    "Rating": [
      {
        "image": "imdb://image.rating",
        "value": 6.6,
        "type": "audience",
        "count": 144
      },
      {
        "image": "rottentomatoes://image.rating.ripe",
        "value": 7.0,
        "type": "critic",
        "count": 47
      },
      {
        "image": "rottentomatoes://image.rating.upright",
        "value": 8.8,
        "type": "audience",
        "count": 503
      }
    ]
  },
  "previousSessionState": "playing",
  "currentSessionState": "paused",
  "Event": "media.pause"
}';

        $jsonData = json_decode($test,true);

        foreach ($jsonData['Metadata']['Guid'] as $guid){

            if (strpos($guid['id'], 'tmdb') !== false){
                $id = str_replace('tmdb://', '', $guid['id']);
            }

        }

        $client = new Client();

        $apiUrl = 'https://api.themoviedb.org/3';

        $apiKey = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJhZmI1ZDg4MTM3ZTM4OWU2M2M4YjVmNDVmNWRhMTg2ZSIsInN1YiI6IjY1NzcwNmEyNTY0ZWM3MDBmZWI1NDA3NiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.B8eXCk-bwC32V5dHtwmtIXl1urYEfCYR0LCeOnckGos';

        $response = $client->get($apiUrl.'/movie/'.$id.'/translations', [
            'headers' => [
                'Authorization' => 'Bearer '.$apiKey,
                'Accept' => 'application/json',
            ],
        ]);


        $data = json_decode($response->getBody(), true);

        foreach ($data['translations'] as $translation){

            if ($translation['iso_639_1'] === "fr"){
                $return = $translation['data']['title'];
            }

        }

        dd($return);

    }
}
