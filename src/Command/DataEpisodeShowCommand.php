<?php

namespace App\Command;

use App\Entity\EpisodeShow;
use App\Repository\EpisodeRepository;
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
    name: 'app:data-episode-show',
    description: 'Add a short description for your command',
)]
class DataEpisodeShowCommand extends Command
{

    private EpisodeRepository $episodeRepository;

    private ObjectManager $manager;


    public function __construct(EpisodeRepository $episodeRepository, ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->episodeRepository = $episodeRepository;
        $this->manager = $managerRegistry->getManager();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $episodes = $this->episodeRepository->findByDateNotNull();

        foreach ($episodes as $episode) {

            $episodeShow = new EpisodeShow();
            $episodeShow->setEpisode($episode);
            $episodeShow->setShowDate($episode->getShowDate());

            $episode->setShowDate(null);

            $this->manager->persist($episodeShow);
            $this->manager->persist($episode);
            $this->manager->flush();

        }

        return Command::SUCCESS;
    }
}
