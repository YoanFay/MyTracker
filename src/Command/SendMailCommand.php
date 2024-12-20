<?php

namespace App\Command;

use App\Entity\SerieUpdate;
use App\Repository\SerieUpdateRepository;
use App\Service\MailService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendMailCommand extends Command
{
    private MailService $mailService;

    private SerieUpdateRepository $serieUpdateRepository;

    public function __construct(MailService $mailService, SerieUpdateRepository $serieUpdateRepository)
    {
        parent::__construct();
        $this->mailService = $mailService;
        $this->serieUpdateRepository = $serieUpdateRepository;
    }

    protected function configure(): void
    {

        $this->setName('app:send-mail');
        $this->setDescription("Pour test l'envoie de mail");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $green = [];
        $yellow = [];
        $red = [];
        $white = [];

        $serieUpdate = $this->serieUpdateRepository->lastWeekUpdate();

        /** @var SerieUpdate $update */
        foreach ($serieUpdate as $update){

            if ($update->getNewStatus() === "Continuing" and $update->getOldStatus() == "Ended"){
                $green[] = ['info' => "Reprise de ".$update->getSerie()->getName()];
            }

        }


        $updates = [
            'green' => $green,
            'yellow' => $yellow,
            'red' => $red,
            'white' => $white,
        ];

        $this->mailService->sendEmail($updates);

        return Command::SUCCESS;
    }
}
