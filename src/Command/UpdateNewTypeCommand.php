<?php

namespace App\Command;

use App\Entity\SerieType;
use App\Repository\SerieRepository;
use App\Repository\SerieTypeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-new-type',
    description: 'Modifie les types de séries avec la nouvelle méthode',
)]
class UpdateNewTypeCommand extends Command
{

    private SerieRepository $serieRepository;

    private SerieTypeRepository $serieTypeRepository;

    private ObjectManager $manager;


    public function __construct(SerieRepository $serieRepository, SerieTypeRepository $serieTypeRepository, ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->serieTypeRepository = $serieTypeRepository;
        $this->manager = $managerRegistry->getManager();

    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $series = $this->serieRepository->findAll();

        foreach ($series as $serie) {

            $type = $serie->getType();

            $serieType = $this->serieTypeRepository->findOneBy(['name' => $type]);

            if (!$serieType) {
                $serieType = new SerieType();

                $serieType->setName($type);

                $this->manager->persist($serieType);
            }

            $serie->setSerieType($serieType);

            $this->manager->persist($serie);
            $this->manager->flush();
        }

        $io->success("C'est tout bon.");

        return Command::SUCCESS;
    }
}
