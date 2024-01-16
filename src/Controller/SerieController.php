<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SerieController extends AbstractController
{
    #[Route('/serie', name: 'app_serie')]
    public function index(): Response
    {
        return $this->render('serie/index.html.twig', [
            'controller_name' => 'SerieController',
        ]);
    }

    #[Route('/serie/add', name: 'app_serie_add')]
    public function addSerie(ManagerRegistry $managerRegistry): Response
    {

        $serie = new Serie();

        $form = $this->createForm(SerieType::class, $serie);

        if ($form->isSubmitted() && $form->isValid()){

            $managerRegistry->getManager()->persist($serie);

            $this->redirectToRoute('home');
        }

        return $this->render('serie/index.html.twig', [
            'controller_name' => 'SerieController',
            'form' => $form->createView(),
        ]);
    }
}
