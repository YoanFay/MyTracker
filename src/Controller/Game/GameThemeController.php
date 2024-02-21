<?php

namespace App\Controller\Game;

use App\Entity\GameTheme;
use App\Form\GameThemeType;
use App\Repository\GameThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/game/theme')]
class GameThemeController extends AbstractController
{
    #[Route('/', name: 'game_theme_index', methods: ['GET'])]
    public function index(GameThemeRepository $gameThemeRepository): Response
    {
        return $this->render('game/game_theme/index.html.twig', [
            'game_themes' => $gameThemeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'game_theme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gameTheme = new GameTheme();
        $form = $this->createForm(GameThemeType::class, $gameTheme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameTheme);
            $entityManager->flush();

            return $this->redirectToRoute('game_theme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_theme/new.html.twig', [
            'game_theme' => $gameTheme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/details', name: 'game_theme_show', methods: ['GET'])]
    public function show(GameThemeRepository $gameThemeRepository, $id): Response
    {
        $gameTheme = $gameThemeRepository->find($id);

        return $this->render('game/game_theme/show.html.twig', [
            'game_theme' => $gameTheme,
        ]);
    }

    #[Route('/{id}/edit', name: 'game_theme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, GameThemeRepository $gameThemeRepository, $id): Response
    {
        $gameTheme = $gameThemeRepository->find($id);

        $form = $this->createForm(GameThemeType::class, $gameTheme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('game_theme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_theme/edit.html.twig', [
            'game_theme' => $gameTheme,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'game_theme_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, GameThemeRepository $gameThemeRepository, $id): Response
    {
        $gameTheme = $gameThemeRepository->find($id);

        if ($this->isCsrfTokenValid('delete'.$gameTheme->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gameTheme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_theme_index', [], Response::HTTP_SEE_OTHER);
    }
}
