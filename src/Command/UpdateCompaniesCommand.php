<?php

namespace App\Command;

use App\Repository\CompanyRepository;
use App\Repository\SerieRepository;
use App\Service\TVDBService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-companies',
    description: 'Add a short description for your command',
)]
class UpdateCompaniesCommand extends Command
{

    private ObjectManager $manager;

    private TVDBService $TVDBService;

    private SerieRepository $serieRepository;

    private CompanyRepository $companyRepository;


    public function __construct(ManagerRegistry $managerRegistry, TVDBService $TVDBService, SerieRepository $serieRepository, CompanyRepository $companyRepository)
    {

        parent::__construct();
        $this->manager = $managerRegistry->getManager();
        $this->TVDBService = $TVDBService;
        $this->serieRepository = $serieRepository;
        $this->companyRepository = $companyRepository;
    }


    protected function configure(): void
    {

        $this->setName('app:update-date');
        $this->setDescription('Pour les date des épisodes et des séries');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $series = $this->serieRepository->noCompanies();

        print_r('Je récupére les séries \n\n');

        foreach ($series as $serie) {

            print_r('Je regarde une série \n\n');

            $data = $this->TVDBService->getData('series/'.$serie->getTvdbId().'/extended');

            $companies = $data['data']['companies'];

            foreach ($companies as $company){

                print_r('Je regarde une company \n\n');

                $searchCompany = $this->companyRepository->findOneBy(['tvdbId' => $company['id']]);

                if(!$searchCompany){
                    $serie->addCompany($this->TVDBService->createCompany($company['id']));
                }else{
                    $serie->addCompany($searchCompany);
                }

            }

            $this->manager->persist($serie);
            $this->manager->flush();

        }

        return Command::SUCCESS;
    }
}
