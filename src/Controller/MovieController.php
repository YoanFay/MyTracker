<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieGenreRepository;
use App\Service\StrSpecialCharsLower;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;

class MovieController extends AbstractController
{
    #[Route('/movie', name: 'movie')]
    public function index(MovieRepository $movieRepository): Response
    {

        $movies = $movieRepository->findAll();

        $moviesByDate = [];
        $dateKeys = [];

        foreach ($movies as $movie) {
            $dateKey = $movie->getShowDate()->format("Y-m-d");

            if (!isset($moviesByDate[$dateKey])) {
                $moviesByDate[$dateKey] = [$movie];
                $dateKeys[] = $dateKey;
            } else {
                $moviesByDate[$dateKey][] = $movie;
            }
        }

        return $this->render('movie/index.html.twig', [
            'moviesByDate' => $moviesByDate,
            'dateKeys' => $dateKeys,
            'controller_name' => 'MovieController',
            'navLinkId' => 'movie',
        ]);
    }

    #[Route('/movie/add', name: 'movie_add')]
    public function addMovie(ManagerRegistry $managerRegistry, Request $request, StrSpecialCharsLower $strSpecialCharsLower): Response
    {

        $movie = new Movie();

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            $movie->setSlug($strSpecialCharsLower->serie($movie->getName()));
            $movie->setUser($this->getUser());

            $managerRegistry->getManager()->persist($movie);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('movie');
        }

        return $this->render('movie/add.html.twig', [
            'controller_name' => 'SerieController',
            'form' => $form->createView(),
            'navLinkId' => 'movie',
        ]);
    }
}
