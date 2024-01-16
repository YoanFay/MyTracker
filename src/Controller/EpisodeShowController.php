<?php

namespace App\Controller;

use App\Entity\EpisodeShow;
use App\Form\EpisodeShowType;
use App\Repository\UsersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function addEpisode(ManagerRegistry $managerRegistry, UsersRepository $usersRepository): Response
    {

        $episode = new EpisodeShow();

        $form = $this->createForm(EpisodeShowType::class, $episode);

        $user = $usersRepository->findOneBy(['plexName' => 'yoan.f8']);

        if ($form->isSubmitted() && $form->isValid()){

            $episode->setUser($user);

            $managerRegistry->getManager()->persist($episode);
            $managerRegistry->getManager()->flush();

            $this->redirectToRoute('home');
        }

        return $this->render('episode_show/add.html.twig', [
            'controller_name' => 'EpisodeShowController',
            'form' => $form->createView(),
        ]);
    }
}
