<?php

namespace App\Controller\Game;

use App\Entity\GameDeveloper;
use App\Form\GameDeveloperType;
use App\Repository\GameDeveloperRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/developer')]
class GameDeveloperController extends AbstractController
{
    #[Route('/', name: 'game_developer_index', methods: ['GET'])]
    public function index(GameDeveloperRepository $gameDeveloperRepository): Response
    {
        return $this->render('game/game_developer/index.html.twig', [
            'game_developers' => $gameDeveloperRepository->findAll(),
        ]);
    }

    #[Route('/add', name: 'game_developer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gameDeveloper = new GameDeveloper();
        $form = $this->createForm(GameDeveloperType::class, $gameDeveloper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameDeveloper);
            $entityManager->flush();

            return $this->redirectToRoute('game_developer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_developer/new.html.twig', [
            'game_developer' => $gameDeveloper,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'game_developer_show', methods: ['GET'])]
    public function show(GameDeveloper $gameDeveloper): Response
    {
        return $this->render('game/game_developer/show.html.twig', [
            'game_developer' => $gameDeveloper,
        ]);
    }

    #[Route('/{id}/edit', name: 'game_developer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GameDeveloper $gameDeveloper, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GameDeveloperType::class, $gameDeveloper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('game_developer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_developer/edit.html.twig', [
            'game_developer' => $gameDeveloper,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'game_developer_delete', methods: ['POST'])]
    public function delete(Request $request, GameDeveloper $gameDeveloper, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gameDeveloper->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gameDeveloper);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_developer_index', [], Response::HTTP_SEE_OTHER);
    }
}