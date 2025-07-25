<?php

namespace App\Controller\Game;

use App\Entity\GameTracker;
use App\Form\GameTrackerType;
use App\Repository\GameRepository;
use App\Repository\GameTrackerRepository;
use App\Service\TimeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/game/tracker')]
class GameTrackerController extends AbstractController
{
    #[Route('/{id}', name: 'game_tracker')]
    public function index(): Response
    {
        return $this->render('game/game_tracker/index.html.twig', [
            'controller_name' => 'GameTrackerController',
            'navLinkId' => 'game',
        ]);
    }
    
    #[Route('/{id}/edit', name: 'game_tracker_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, GameTrackerRepository $gameTrackerRepository, GameRepository $gameRepository, TimeService $timeService, int $id): Response
    {
        $gameTracker = $gameTrackerRepository->find($id);
        
        if (!$gameTracker){
            
            $gameTracker = new GameTracker();
            $gameTracker->setId($id);

            $game = $gameRepository->find($id);

            if (!$game){
                $this->addFlash('error', 'Ce jeu n\'existe pas');
                return $this->redirectToRoute('game');
            }
            
            $gameTracker->setGame($game);
        }
        
        $form = $this->createForm(GameTrackerType::class, $gameTracker);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var array<string, mixed> $data */
            $data = $request->request->get('game_tracker');
            
            if($data['endTime'] !== "" && $data['endTime'] !== "0h0"){

                $explode = explode("h", $data["endTime"]);

                $heures = $explode[0] !== "" ? intval($explode[0]) : null;
                $minutes = $explode[1] !== "" ? intval($explode[1]) : null;

                /** @var int $endTime */
                $endTime = $timeService->convertirHeureMinute($heures, $minutes);

                $gameTracker->setEndTime($endTime);
            }else{
                $gameTracker->setEndTime(null);
            }
            
            if($data['completeTime'] !== "" && $data['completeTime'] !== "0h0"){

                $explode = explode("h", $data["completeTime"]);

                $heures = $explode[0] !== "" ? intval($explode[0]) : null;
                $minutes = $explode[1] !== "" ? intval($explode[1]) : null;

                /** @var int $completeTime */
                $completeTime = $timeService->convertirHeureMinute($heures, $minutes);

                $gameTracker->setCompleteTime($completeTime);
            }else{
                $gameTracker->setCompleteTime(null);
            }
            
            $entityManager->persist($gameTracker);
            $entityManager->flush();
            
            return $this->redirectToRoute('game_details', [
                'id' => $id
            ]);
        }
        
        if($gameTracker->getEndTime()){
            $form->get('endTime')->setData($timeService->convertirSecondes($gameTracker->getEndTime()));
        }
        if($gameTracker->getCompleteTime()){
            $form->get('completeTime')->setData($timeService->convertirSecondes($gameTracker->getCompleteTime()));
        }
        
        return $this->render('game/game_tracker/edit.html.twig', [
            'controller_name' => 'GameTrackerController',
            'game' => $gameTracker->getGame(),
            'form' => $form->createView(),
            'navLinkId' => 'game',
        ]);
    }
}
