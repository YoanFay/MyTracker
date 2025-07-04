<?php

namespace App\Controller\Game;

use App\Entity\GamePlatform;
use App\Form\GamePlatformAddType;
use App\Form\GamePlatformType;
use App\Repository\GamePlatformRepository;
use App\Service\IGDBService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/platform')]
class GamePlatformController extends AbstractController
{
    #[Route('/', name: 'game_platform', methods: ['GET'])]
    public function index(GamePlatformRepository $gamePlatformRepository): Response
    {
        return $this->render('game/game_platform/index.html.twig', [
            'game_platforms' => $gamePlatformRepository->findAll(),
            'navLinkId' => 'game_platform',
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

            return $this->redirectToRoute('game_platform', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_platform/new.html.twig', [
            'game_platform' => $gamePlatform,
            'form' => $form,
            'navLinkId' => 'game_platform',
        ]);
    }

    #[Route('/{id}/details', name: 'game_platform_show', methods: ['GET'])]
    public function show(GamePlatformRepository $gamePlatformRepository, int $id): Response
    {
        $gamePlatform = $gamePlatformRepository->find($id);

        return $this->render('game/game_platform/show.html.twig', [
            'game_platform' => $gamePlatform,
            'navLinkId' => 'game_platform',
        ]);
    }

    #[Route('/{id}/edit', name: 'game_platform_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, GamePlatformRepository $gamePlatformRepository, int $id): Response
    {
        $gamePlatform = $gamePlatformRepository->find($id);

        $form = $this->createForm(GamePlatformType::class, $gamePlatform);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('game_platform', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_platform/edit.html.twig', [
            'game_platform' => $gamePlatform,
            'form' => $form,
            'navLinkId' => 'game_platform',
        ]);
    }

    #[Route('/{id}/delete', name: 'game_platform_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, GamePlatformRepository $gamePlatformRepository, int $id): Response
    {
        $gamePlatform = $gamePlatformRepository->find($id);

        if ($gamePlatform) {
            /** @var ?string $token */
            $token = $request->request->get('_token');

            if ($this->isCsrfTokenValid('delete'.$gamePlatform->getId(), $token)) {
                $entityManager->remove($gamePlatform);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('game_platform', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/add', name: 'game_platform_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, IGDBService $IGDBService): Response
    {
        $form = $this->createForm(GamePlatformAddType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();

            $body = 'fields *;where name = "'.$formData['name'].'";';

            $dataPlatform = $IGDBService->getData('platforms', $body);

            $gamePlatform = new GamePlatform();

            $gamePlatform->setName($formData['name']);
            $gamePlatform->setIgdbId($dataPlatform['id']);

            $entityManager->persist($gamePlatform);
            $entityManager->flush();

            return $this->redirectToRoute('game_platform', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('game/game_platform/new.html.twig', [
            'form' => $form,
            'navLinkId' => 'game_platform',
        ]);
    }
}
