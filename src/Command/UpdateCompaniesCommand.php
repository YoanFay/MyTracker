<?php

namespace App\Command;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Repository\SerieRepository;
use App\Service\TVDBService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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

    private Client $client;


    public function __construct(ManagerRegistry $managerRegistry, TVDBService $TVDBService, SerieRepository $serieRepository, CompanyRepository $companyRepository, Client $client)
    {

        parent::__construct();
        $this->manager = $managerRegistry->getManager();
        $this->TVDBService = $TVDBService;
        $this->serieRepository = $serieRepository;
        $this->companyRepository = $companyRepository;
        $this->client = $client;
    }


    protected function configure(): void
    {

        $this->setName('app:update-date');
        $this->setDescription('Pour les date des épisodes et des séries');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        /*$series = $this->serieRepository->noCompanies();

        foreach ($series as $serie) {

            $data = $this->TVDBService->getData('/series/'.$serie->getTvdbId().'/extended');

            $companies = $data['data']['companies'];

            foreach ($companies as $company){

                $searchCompany = $this->companyRepository->findOneBy(['tvdbId' => $company['id']]);

                if(!$searchCompany){
                    $serie->addCompany($this->TVDBService->createCompany($company['id']));
                }else{
                    $serie->addCompany($searchCompany);
                }

            }

            $this->manager->persist($serie);
            $this->manager->flush();

        }*/

        $series = $this->serieRepository->animeNoCompanies();

        $query = 'query ($search: String) { Media (search: $search, type: ANIME) { title{english}, type, status ,studios{nodes{id, name, isAnimationStudio}}}}';

        foreach ($series as $serie) {

            $variables = [
                "search" => $serie->getName()
            ];

            try {
                $response = $this->client->post('https://graphql.anilist.co', [
                    'json' => [
                        'query' => $query,
                        'variables' => $variables,
                    ]
                ]);

            } catch (\Exception|GuzzleException $e) {
                continue;
            }

            if ($response->getHeader('X-RateLimit-Remaining')[0] == 0) {
                sleep(60);
            }

            $data = json_decode($response->getBody(), true);

            $data = $data['data']['Media'];

            $studio = null;

            foreach ($data['studios']['nodes'] as $node) {

                if ($node['isAnimationStudio']) {
                    $studio = $node;
                }

            }

            $company = $this->companyRepository->findOneBy(['name' => $studio['name'], 'type' => 'Studio']);

            if(!$company){

                $company = new Company();

                $company->setName($studio['name']);
                $company->setType('Studio');

            }

            $company->addSeries($serie);

            $this->manager->persist($company);
            $this->manager->flush();

        }

        return Command::SUCCESS;
    }
}
