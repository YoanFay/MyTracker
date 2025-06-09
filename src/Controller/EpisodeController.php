<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\EpisodeShow;
use App\Form\EpisodeType;
use App\Repository\UsersRepository;
use Bugsnag\DateTime\Date;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SerieRepository;
use App\Repository\EpisodeRepository;
use DateTime;

#[Route('/episode')]
class EpisodeController extends AbstractController
{

    #[Route('/add/{id}', name: 'episode_add')]
    public function addEpisode(ManagerRegistry $managerRegistry, UsersRepository $usersRepository, EpisodeRepository $episodeRepository, SerieRepository $serieRepository, Request $request, int $id = null): Response
    {

        $episode = new Episode();

        if ($id){

            $serie = $serieRepository->find($id);

            $episode->setSerie($serie);

        }

        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var array<string, mixed> $episodeData */
            $episodeData = $request->request->get('episode');

            /** @var DateTime $date */
            $date = DateTime::createFromFormat('d/m/Y H:i', $episodeData['showDate']);

            $checkEpisode = $episodeRepository->findOneBy(['serie' => $episode->getSerie(), 'saisonNumber' => $episode->getSaisonNumber(), 'episodeNumber' => $episode->getEpisodeNumber()]);

            if ($checkEpisode) {

                $episode = $checkEpisode;

            } else {

                $user = $usersRepository->findOneBy(['plexName' => 'yoan.f8']);

                $episode->setUser($user);

                $episode->setDuration($episode->getDuration() * 60000);

                $managerRegistry->getManager()->persist($episode);
                $managerRegistry->getManager()->flush();
            }

            $episodeShow = new EpisodeShow();
            $episodeShow->setShowDate($date);
            $episodeShow->setEpisode($episode);

            $managerRegistry->getManager()->persist($episodeShow);
            $managerRegistry->getManager()->flush();

            if ($id){
                return $this->redirectToRoute('serie_detail', [
                    'id' => $id
                ]);
            }

            return $this->redirectToRoute('historique');
        }

        return $this->render('episode/add.html.twig', [
            'id' => $id,
            'controller_name' => 'EpisodeController',
            'form' => $form->createView(),
            'navLinkId' => 'episode_add',
        ]);
    }
}
                                    