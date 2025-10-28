<?php

namespace App\Command;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-music-anime',
    description: 'Pour ajouter le tag anime à certaines musique',
)]
class UpdateMusicAnimeTagCommand extends Command
{


    private ObjectManager $manager;


    public function __construct(ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->manager = $managerRegistry->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {



        return Command::SUCCESS;
    }
}
