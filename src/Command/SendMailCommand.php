<?php

namespace App\Command;

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

        $serieUpdate = $this->serieUpdateRepository->lastWeekUpdate();

        $green = [];
        $yellow = [];
        $red = [];
        $white = [];


        $update = [
            'green' => $green,
            'yellow' => $yellow,
            'red' => $red,
            'white' => $white,
        ];

        $this->mailService->sendEmail($update);

        return Command::SUCCESS;
    }
}
