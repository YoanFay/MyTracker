<?php

namespace App\Command;

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
    private $mailService;

    public function __construct(MailService $mailService)
    {
        parent::__construct();
        $this->mailService = $mailService;
    }

    protected function configure(): void
    {

        $this->setName('app:send-mail');
        $this->setDescription("Pour test l'envoie de mail");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $test = [
            'green' => [
                ['info' => 'ligne 1'],
                ['info' => 'ligne 2'],
                ['info' => 'ligne 3'],
                ['info' => 'ligne 4'],
                ['info' => 'ligne 5'],
                ['info' => 'ligne 6'],
            ],
        ];

        $this->mailService->sendEmail($test);

        return Command::SUCCESS;
    }
}
