<?php

namespace App\Controller\Game;

use App\Entity\Game;
use App\Entity\GameMode;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\GameModeRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {
        return $this->render('game/game/index.html.twig', [
            'games' => $gameRepository->findAllByName(),
            'navLinkId' => 'game'
        ]);
    }

    #[Route('/new', name: 'game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game/new.html.twig', [
            'game' => $game,
            'form' => $form,
            'navLinkId' => 'game'
        ]);
    }

    #[Route('/{id}/details', name: 'game_show', methods: ['GET'])]
    public function show(GameRepository $gameRepository, $id): Response
    {

        $game = $gameRepository->find($id);

        return $this->render('game/game/show.html.twig', [
            'game' => $game,
            'navLinkId' => 'game'
        ]);
    }

    #[Route('/{id}/edit', name: 'game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, GameRepository $gameRepository, $id): Response
    {

        $game = $gameRepository->find($id);

        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
            'navLinkId' => 'game'
        ]);
    }

    #[Route('/{id}/delete', name: 'game_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, GameRepository $gameRepository, $id): Response
    {

        $game = $gameRepository->find($id);

        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/test', name: 'game_test', methods: ['GET'])]
    public function test(Request $request, EntityManagerInterface $entityManager, GameRepository $gameRepository, GameModeRepository $gameModeRepository): Response
    {
        
        //$cover = 'https:'.str_replace('/t_thumb/', '/t_cover_big/', $dataGameCover[0]['url']);
        
        $games = [136498, 140427, 7386, 119171, 119388];

        $client = new Client();

        $response = $client->post("https://id.twitch.tv/oauth2/token?client_id=sd5xdt5w2lkjr7ws92fxjdlicvb5u2&client_secret=tymefepntjuva1n9ipa3lkjts2pmdh&grant_type=client_credentials", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        
        $token = "Bearer ".$data['access_token'];

        foreach($games as $idGame){
            
        $game = new Game();

        $response = $client->post("https://api.igdb.com/v4/games", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                'Authorization' => $token
            ],
            'body' => 'fields name,game_modes,genres,themes; where id = '.$idGame.';'
        ]);

        $dataGame = json_decode($response->getBody(), true)[0];
        
        dump($dataGame);
        
        $game->setName($dataGame['name']);

        $response = $client->post("https://api.igdb.com/v4/alternative_names", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                'Authorization' => $token
            ],
            'body' => 'fields *;where game = '.$idGame.' & comment = "French title";'
        ]);

        $dataName = json_decode($response->getBody(), true);
        
        if($dataName !== []){
            $game->setName($dataName[0]['name']);
        }
        
        foreach($dataGame['game_modes'] as $gameModeId){
            $gameMode = $gameModeRepository->find($gameModeId);
            
            if(!$gameMode){
                

        $response = $client->post("https://api.igdb.com/v4/game_modes", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Client-ID' => 'sd5xdt5w2lkjr7ws92fxjdlicvb5u2',
                'Authorization' => $token
            ],
            'body' => 'fields name;where id = '.$gameModeId.';'
        ]);

        $dataGameMode = json_decode($response->getBody(), true)[0];
        
        $gameMode = new GameMode();
        
        $gameMode->setImdbId($gameModeId);
        $gameMode->setName($dataGameMode['name']);
        
        $entityManager->persist($gameMode);
        $entityManager->flush();
        
            }
        }
        
        dump($game);
        
        }

        dd('Fin Test');
    }
}
