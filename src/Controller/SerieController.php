<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Form\SerieEditType;
use App\Form\SerieAnimeEditType;
use App\Repository\SerieRepository;
use App\Repository\EpisodeShowRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SerieController extends AbstractController
{

    #[Route('/serie', name: 'serie')]
    public function index(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findAll();

        return $this->render('serie/index.html.twig', [
            'controller_name' => 'SerieController',
            'series' => $series,
        ]);
    }

    #[Route('/serie/detail/{id}', name: 'serie_detail')]
    public function detail(SerieRepository $serieRepository, EpisodeShowRepository $episodeShowRepository, $id): Response
    {

        $serie = $serieRepository->findOneBy(['id' => $id]);
        $totalDuration = $episodeShowRepository->getDurationBySerie($id);
        $countEpisode = $episodeShowRepository->getCountBySerie($id);
        
        $genres = $serie->getGenres();
        $animeGenres = $serie->getAnimeGenres();
        $animeThemes = $serie->getAnimeThemes();
        
        $tagTypes = [];
        
        foreach($serie->getTags() as $tag){
            
            if(!array_key_exists($tag->getTagsType()->getNameFra(), $tagTypes)){
                $tagTypes[$tag->getTagsType()->getNameFra()] = [];
            }
            
            array_push($tagTypes[$tag->getTagsType()->getNameFra()], $tag);
        }

        return $this->render('serie/details.html.twig', [
            'controller_name' => 'SerieController',
            'serie' => $serie,
            'totalDuration'=> $totalDuration['COUNT'],
            'countEpisode' => $countEpisode['COUNT'],
            'genres' => $genres,
            'tagTypes' => $tagTypes,
            'animeGenres' => $animeGenres,
            'animeThemes' => $animeThemes,
        ]);
    }

    #[Route('/serie/add', name: 'serie_add')]
    public function addSerie(ManagerRegistry $managerRegistry, Request $request): Response
    {

        $serie = new Serie();

        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $serie->setType($form->get('type')->getData());

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('episode');
        }

        return $this->render('serie/add.html.twig', [
            'controller_name' => 'SerieController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/serie/edit/{id}', name: 'serie_edit')]
    public function editSerie(ManagerRegistry $managerRegistry, SerieRepository $serieRepository, Request $request, $id): Response
    {

        $serie = $serieRepository->findOneBy(['id' => $id]);
        
        if($serie->getType() === "Anime"){
            return $this->redirectToRoute('serie_edit_anime', ['id' => $id]);
        }

        $form = $this->createForm(SerieEditType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $passwordTest = $form->get('password')->getData();

            $serie->setType($form->get('type')->getData());

            if (!password_verify($passwordTest, $_ENV['PASSWORD_USER'])){
                $this->addFlash('error', 'Mot de passe incorrect');

                return $this->redirectToRoute('episode_add');
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('serie_detail', ['id' => $id]);
        }

        return $this->render('serie/add.html.twig', [
            'controller_name' => 'SerieController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/serie/editAnime/{id}', name: 'serie_edit_anime')]
    public function editSerieAnime(ManagerRegistry $managerRegistry, SerieRepository $serieRepository, Request $request, $id): Response
    {

        $serie = $serieRepository->findOneBy(['id' => $id]);
        
        if($serie->getType() !== "Anime"){
            return $this->redirectToRoute('serie_edit', ['id' => $id]);
        }
        
        $oldAnimeGenre = $serie->getAnimeGenres()->getValues();
        $oldAnimeTheme = $serie->getAnimeThemes()->getValues();

        $form = $this->createForm(SerieAnimeEditType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $passwordTest = $form->get('password')->getData();

            $serie->setType($form->get('type')->getData());

            if (!password_verify($passwordTest, $_ENV['PASSWORD_USER'])){
                $this->addFlash('error', 'Mot de passe incorrect');

                return $this->redirectToRoute('episode_add');
            }
            
            $animeGenres = $serie->getAnimeGenres()->getValues();
            
            foreach($oldAnimeGenre as $genre){
                $serie->addAnimeGenre($genre);
                $serie->removeAnimeGenre($genre);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();
            
            foreach($animeGenres as $genre){
                $serie->removeAnimeGenre($genre);
                $serie->addAnimeGenre($genre);
            }
            
            $animeThemes = $serie->getAnimeThemes()->getValues();
            
            foreach($oldAnimeTheme as $theme){
                $serie->addAnimeTheme($theme);
                $serie->removeAnimeTheme($theme);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();
            
            foreach($animeThemes as $theme){
                $serie->removeAnimeTheme($theme);
                $serie->addAnimeTheme($theme);
            }

            $managerRegistry->getManager()->persist($serie);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('serie_detail', ['id' => $id]);
        }

        return $this->render('serie/add.html.twig', [
            'controller_name' => 'SerieController',
            'form' => $form->createView(),
        ]);
    }
}
