<?php

namespace App\Controller;

use App\Entity\EpisodeShow;
use App\Form\EpisodeShowType;
use App\Repository\UsersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EpisodeShowController extends AbstractController
{
    #[Route('/episode', name: 'app_episode')]
    public function index(): Response
    {
        return $this->render('episode_show/index.html.twig', [
            'controller_name' => 'EpisodeShowController',
        ]);
    }

    #[Route('/episode/add', name: 'app_episode_add')]
    public function addEpisode(ManagerRegistry $managerRegistry, UsersRepository $usersRepository, Request $request): Response
    {

        $episode = new EpisodeShow();

        $form = $this->createForm(EpisodeShowType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $user = $usersRepository->findOneBy(['plexName' => 'yoan.f8']);

            $episode->setUser($user);

            $episode->setDuration($episode->getDuration() * 60000);

            $managerRegistry->getManager()->persist($episode);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('episode_show/add.html.twig', [
            'controller_name' => 'EpisodeShowController',
            'form' => $form->createView(),
        ]);
    }
}
