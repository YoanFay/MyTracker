<?php

namespace App\Controller\Game;

use App\Entity\GameGenre;
use App\Form\GameGenreType;
use App\Repository\GameGenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/game/genre')]
class GameGenreController extends AbstractController
{
    #[Route('/', name: 'game_genre_index', methods: ['GET'])]
    public function index(GameGenreRepository $gameGenreRepository): Response
    {
        return $this->render('game/game_genre/index.html.twig', [
            'game_genres' => $gameGenreRepository->findAll(),
            'navLinkId' => 'game-genre',
        ]);
    }

    #[Route('/new', name: 'game_genre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gameGenre = new GameGenre();
        $form = $this->createForm(GameGenreType::class, $gameGenre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameGenre);
            $entityManager->flush();

            return $this->redirectToRoute('game_genre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_genre/new.html.twig', [
            'game_genre' => $gameGenre,
            'form' => $form,
            'navLinkId' => 'game-genre',
        ]);
    }

    #[Route('/{id}/details', name: 'game_genre_show', methods: ['GET'])]
    public function show(GameGenreRepository $gameGenreRepository, $id): Response
    {
        $gameGenre = $gameGenreRepository->find($id);

        return $this->render('game/game_genre/show.html.twig', [
            'game_genre' => $gameGenre,
            'navLinkId' => 'game-genre',
        ]);
    }

    #[Route('/{id}/edit', name: 'game_genre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, GameGenreRepository $gameGenreRepository, $id): Response
    {
        $gameGenre = $gameGenreRepository->find($id);

        $form = $this->createForm(GameGenreType::class, $gameGenre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('game_genre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_genre/edit.html.twig', [
            'game_genre' => $gameGenre,
            'form' => $form,
            'navLinkId' => 'game-genre',
        ]);
    }

    #[Route('/{id}/delete', name: 'game_genre_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, GameGenreRepository $gameGenreRepository, $id): Response
    {
        $gameGenre = $gameGenreRepository->find($id);

        if ($this->isCsrfTokenValid('delete'.$gameGenre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gameGenre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_genre_index', [], Response::HTTP_SEE_OTHER);
    }
}
