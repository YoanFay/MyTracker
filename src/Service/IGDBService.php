<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpKernel\KernelInterface;

class IGDBService
{

    private KernelInterface $kernel;


    public function __construct(KernelInterface $kernel)
    {

        $this->kernel = $kernel;
    }

    public function auth(): string
    {

        $client = new Client();

        $response = $client->post("https://id.twitch.tv/oauth2/token?client_id=sd5xdt5w2lkjr7ws92fxjdlicvb5u2&client_secret=tymefepntjuva1n9ipa3lkjts2pmdh&grant_type=client_credentials", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        return "Bearer ".$data['access_token'];

    }


    /**
     * @throws GuzzleException
     */
    public function getData($category, $body){

        $client = new Client();

        $token = $this->auth();

        $response = $client->post("https://api.igdb.com/v4/".$category, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                'Authorization' => $token
            ],
            'body' => $body
        ]);

        return json_decode($response->getBody(), true);

    }
}
