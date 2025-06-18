<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\MovieShow;
use App\Entity\Users;
use App\Form\MovieType;
use App\Repository\MovieShowRepository;
use App\Service\TMDBService;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;

#[Route('/movie')]
class MovieController extends AbstractController
{
    #[Route('/', name: 'movie')]
    public function index(): Response
    {
        return $this->render('movie/index.html.twig', [
            'controller_name' => 'MovieController',
            'navLinkId' => 'movie',
        ]);
    }


    /**
     * @throws NonUniqueResultException
     */
    #[Route('/list', name: 'movie_list')]
    public function movieList(MovieRepository $movieRepository, MovieShowRepository $movieShowRepository, Request $request): Response
    {

        $text = $request->request->get('text');

        $movies = $movieRepository->getByLikeName($text);

        $movieTab = [];

        foreach ($movies as $movie) {

            $lastShow = $movieShowRepository->findLastShowByMovie($movie);

            $movieTab[] = [
                'id' => $movie->getId(),
                'name' => $movie->getName(),
                'artwork' => $movie->getArtwork(),
                'lastDate' => $lastShow?->getShowDate(),
                'entity' => $movie,
            ];

        }

        uasort($movieTab, function ($a, $b) {

            // Utilise strtotime pour convertir les dates en timestamps pour une comparaison facile
            $dateA = $a['lastDate'];
            $dateB = $b['lastDate'];

            // Retourne -1 si $dateA est inférieur à $dateB, 1 si supérieur, 0 si égal
            return $dateB <=> $dateA;
        });

        return $this->render('movie/list.html.twig', [
            'controller_name' => 'MovieController',
            'movies' => $movieTab,
            'navLinkId' => 'movie',
        ]);
    }

    #[Route('/add', name: 'movie_add')]
    public function addMovie(ManagerRegistry $managerRegistry, Request $request, TMDBService $TMDBService): Response
    {

        $movie = new Movie();

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            /** @var array<string, mixed> $movieData */
            $movieData = $request->request->get('movie');

            $TMDBService->updateInfo($movie);

            /** @var Users $user */
            $user = $this->getUser();
            $movie->setUser($user);

            $managerRegistry->getManager()->persist($movie);
            $managerRegistry->getManager()->flush();

            $movieShow = new MovieShow();
            $movieShow->setMovie($movie);

            /** @var DateTime $showDate */
            $showDate = DateTime::createFromFormat('d/m/Y H:i', $movieData['showDate']);

            $movieShow->setShowDate($showDate);

            $managerRegistry->getManager()->persist($movieShow);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('movie');
        }

        return $this->render('movie/add.html.twig', [
            'controller_name' => 'MovieController',
            'form' => $form->createView(),
            'navLinkId' => 'movie',
        ]);
    }

    #[Route('/detail/{id}', name: 'movie_detail')]
    public function detail(MovieRepository $movieRepository, int $id): Response
    {

        $movie = $movieRepository->findOneBy(['id' => $id]);

        if (!$movie){

                $this->addFlash('error', 'Film non trouvé');

                return $this->redirectToRoute('movie');

        }

        $movieGenres = $movie->getMovieGenres();

        return $this->render('movie/details.html.twig', [
            'controller_name' => 'MovieController',
            'movie' => $movie,
            'movieGenres' => $movieGenres,
            'navLinkId' => 'movie',
        ]);
    }
}
