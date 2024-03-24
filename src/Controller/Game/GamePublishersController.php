<?php

namespace App\Controller\Game;

use App\Entity\GamePublishers;
use App\Form\GamePublishersType;
use App\Repository\GamePublishersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/game/publishers')]
class GamePublishersController extends AbstractController
{
    #[Route('/', name: 'game_publishers_index', methods: ['GET'])]
    public function index(GamePublishersRepository $gamePublishersRepository): Response
    {
        return $this->render('game/game_publishers/index.html.twig', [
            'game_publishers' => $gamePublishersRepository->findAll(),
            'navLinkId' => 'game_publisher',
        ]);
    }

    #[Route('/new', name: 'game_publishers_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gamePublisher = new GamePublishers();
        $form = $this->createForm(GamePublishersType::class, $gamePublisher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gamePublisher);
            $entityManager->flush();

            return $this->redirectToRoute('game_publishers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_publishers/new.html.twig', [
            'game_publisher' => $gamePublisher,
            'form' => $form,
            'navLinkId' => 'game_publisher',
        ]);
    }

    #[Route('/{id}/details', name: 'game_publishers_show', methods: ['GET'])]
    public function show(GamePublishersRepository $gamePublishersRepository, $id): Response
    {
        $gamePublisher = $gamePublishersRepository->find($id);

        return $this->render('game/game_publishers/show.html.twig', [
            'game_publisher' => $gamePublisher,
            'navLinkId' => 'game_publisher',
        ]);
    }

    #[Route('/{id}/edit', name: 'game_publishers_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, GamePublishersRepository $gamePublishersRepository, $id): Response
    {
        $gamePublisher = $gamePublishersRepository->find($id);

        $form = $this->createForm(GamePublishersType::class, $gamePublisher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('game_publishers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_publishers/edit.html.twig', [
            'game_publisher' => $gamePublisher,
            'form' => $form,
            'navLinkId' => 'game_publisher',
        ]);
    }

    #[Route('/{id}/delete', name: 'game_publishers_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, GamePublishersRepository $gamePublishersRepository, $id): Response
    {
        $gamePublisher = $gamePublishersRepository->find($id);

        if ($this->isCsrfTokenValid('delete'.$gamePublisher->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gamePublisher);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_publishers_index', [], Response::HTTP_SEE_OTHER);
    }
}
