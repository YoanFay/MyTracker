<?php

namespace App\Controller\Game;

use App\Entity\GameSerie;
use App\Form\GameSerieType;
use App\Repository\GameSerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/serie')]
class GameSerieController extends AbstractController
{
    #[Route('/', name: 'game_serie', methods: ['GET'])]
    public function index(GameSerieRepository $gameSerieRepository): Response
    {
        return $this->render('game/game_serie/index.html.twig', [
            'game_series' => $gameSerieRepository->findAll(),
            'navLinkId' => 'game_serie'
        ]);
    }

    #[Route('/new', name: 'game_serie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gameSerie = new GameSerie();
        $form = $this->createForm(GameSerieType::class, $gameSerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameSerie);
            $entityManager->flush();

            return $this->redirectToRoute('game_serie', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_serie/new.html.twig', [
            'game_serie' => $gameSerie,
            'form' => $form,
            'navLinkId' => 'game_serie'
        ]);
    }

    #[Route('/{id}/details', name: 'game_serie_show', methods: ['GET'])]
    public function show(GameSerieRepository $gameSerieRepository, int $id): Response
    {
        $gameSerie = $gameSerieRepository->find($id);

        return $this->render('game/game_serie/show.html.twig', [
            'game_serie' => $gameSerie,
            'navLinkId' => 'game_serie'
        ]);
    }

    #[Route('/{id}/edit', name: 'game_serie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, GameSerieRepository $gameSerieRepository, int $id): Response
    {
        $gameSerie = $gameSerieRepository->find($id);

        $form = $this->createForm(GameSerieType::class, $gameSerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('game_serie', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_serie/edit.html.twig', [
            'game_serie' => $gameSerie,
            'form' => $form,
            'navLinkId' => 'game_serie'
        ]);
    }

    #[Route('/{id}/delete', name: 'game_serie_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, GameSerieRepository $gameSerieRepository, int $id): Response
    {
        $gameSerie = $gameSerieRepository->find($id);

        /** @var ?string $token */
        $token = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$gameSerie->getId(), $token)) {
            $entityManager->remove($gameSerie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_serie', [], Response::HTTP_SEE_OTHER);
    }
}
