<?php

namespace App\Controller\Game;

use App\Entity\GamePlatform;
use App\Form\GamePlatformType;
use App\Repository\GamePlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/platform')]
class GamePlatformController extends AbstractController
{
    #[Route('/', name: 'game_platform_index', methods: ['GET'])]
    public function index(GamePlatformRepository $gamePlatformRepository): Response
    {
        return $this->render('game/game_platform/index.html.twig', [
            'game_platforms' => $gamePlatformRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'game_platform_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gamePlatform = new GamePlatform();
        $form = $this->createForm(GamePlatformType::class, $gamePlatform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gamePlatform);
            $entityManager->flush();

            return $this->redirectToRoute('game_platform_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_platform/new.html.twig', [
            'game_platform' => $gamePlatform,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'game_platform_show', methods: ['GET'])]
    public function show(GamePlatform $gamePlatform): Response
    {
        return $this->render('game/game_platform/show.html.twig', [
            'game_platform' => $gamePlatform,
        ]);
    }

    #[Route('/{id}/edit', name: 'game_platform_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GamePlatform $gamePlatform, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GamePlatformType::class, $gamePlatform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('game_platform_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_platform/edit.html.twig', [
            'game_platform' => $gamePlatform,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'game_platform_delete', methods: ['POST'])]
    public function delete(Request $request, GamePlatform $gamePlatform, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gamePlatform->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gamePlatform);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_platform_index', [], Response::HTTP_SEE_OTHER);
    }
}