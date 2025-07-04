<?php

namespace App\Controller\Game;

use App\Entity\GameMode;
use App\Form\GameModeType;
use App\Repository\GameModeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/mode')]
class GameModeController extends AbstractController
{
    #[Route('/', name: 'game_mode', methods: ['GET'])]
    public function index(GameModeRepository $gameModeRepository): Response
    {

        return $this->render('game/game_mode/index.html.twig', [
            'game_modes' => $gameModeRepository->findAll(),
            'navLinkId' => 'game_mode',
        ]);
    }


    #[Route('/new', name: 'game_mode_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $gameMode = new GameMode();
        $form = $this->createForm(GameModeType::class, $gameMode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameMode);
            $entityManager->flush();

            return $this->redirectToRoute('game_mode', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_mode/new.html.twig', [
            'game_mode' => $gameMode,
            'form' => $form,
            'navLinkId' => 'game_mode',
        ]);
    }


    #[Route('/{id}/details', name: 'game_mode_show', methods: ['GET'])]
    public function show(GameModeRepository $gameModeRepository, int $id): Response
    {

        $gameMode = $gameModeRepository->find($id);
        return $this->render('game/game_mode/show.html.twig', [
            'game_mode' => $gameMode,
            'navLinkId' => 'game_mode',
        ]);
    }


    #[Route('/{id}/edit', name: 'game_mode_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, GameModeRepository $gameModeRepository, int $id): Response
    {

        $gameMode = $gameModeRepository->find($id);

        $form = $this->createForm(GameModeType::class, $gameMode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('game_mode', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_mode/edit.html.twig', [
            'game_mode' => $gameMode,
            'form' => $form,
            'navLinkId' => 'game_mode',
        ]);
    }


    #[Route('/{id}/delete', name: 'game_mode_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, GameModeRepository $gameModeRepository, int $id): Response
    {

        $gameMode = $gameModeRepository->find($id);

        if ($gameMode) {
            /** @var ?string $token */
            $token = $request->request->get('_token');

            if ($this->isCsrfTokenValid('delete'.$gameMode->getId(), $token)) {
                $entityManager->remove($gameMode);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('game_mode', [], Response::HTTP_SEE_OTHER);
    }
}
