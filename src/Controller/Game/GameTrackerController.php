<?php

namespace App\Controller\Game;

use App\Entity\GameTracker;
use App\Form\GameThemeType;
use App\Form\GameTrackerType;
use App\Repository\GameRepository;
use App\Repository\GameTrackerRepository;
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
        ]);
    }

    #[Route('/{id}/edit', name: 'game_tracker_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, GameTrackerRepository $gameTrackerRepository, GameRepository $gameRepository, $id): Response
    {
        $gameTracker = $gameTrackerRepository->find($id);

        if (!$gameTracker){

            $gameTracker = new GameTracker();
            $gameTracker->setId($id);

            $game = $gameRepository->find($id);

            $gameTracker->setGame($game);
        }

        $form = $this->createForm(GameTrackerType::class, $gameTracker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameTracker);
            $entityManager->flush();

            return $this->redirectToRoute('game_index');
        }

        return $this->render('game/game_tracker/edit.html.twig', [
            'controller_name' => 'GameTrackerController',
            'form' => $form->createView(),
        ]);
    }
}
