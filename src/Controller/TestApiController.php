<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use App\Repository\SerieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestApiController extends AbstractController
{

    #[Route('/test/api', name: 'app_test_api')]
    public function index(): Response
    {

        return $this->render('test_api/index.html.twig', [
            'controller_name' => 'TestApiController',
        ]);
    }
}
