<?php

namespace App\Command;

use App\Entity\SerieUpdate;
use App\Repository\SerieUpdateRepository;
use App\Service\MailService;
use App\Service\TimeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendMailCommand extends Command
{
    private MailService $mailService;

    private TimeService $timeService;

    private SerieUpdateRepository $serieUpdateRepository;

    public function __construct(MailService $mailService, TimeService $timeService, SerieUpdateRepository $serieUpdateRepository)
    {
        parent::__construct();
        $this->mailService = $mailService;
        $this->timeService = $timeService;
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
        $orange = [];
        $red = [];
        $white = [];

        $serieUpdate = $this->serieUpdateRepository->lastWeekUpdate();

        /** @var SerieUpdate $update */
        foreach ($serieUpdate as $update){

            $name = $update->getSerie()->getName();

            if($update->getNewStatus() === "Ended"){
                $red[] = ['info' => $name." est terminé"];
            }
            elseif ($update->getNewStatus() === "Continuing" and $update->getOldStatus() == "Ended"){
                $green[] = ['info' => "Reprise de ".$name];
            }
            elseif($update->getNewNextAired() and $update->getNewStatus() !== "Upcoming"){
                $text = $name." - Le prochain épisode sera ".$this->timeService->dateUpcoming($update->getNewNextAired(), $update->getNextAiredType());

                if($update->getOldNextAired() === null or $update->getNextAiredType() === "year" or $update->getNextAiredType() === "month" or ($update->getNextAiredType() == null and ($update->getOldAiredType() === "month" or $update->getOldAiredType() === "year"))){
                    $green[] = ['info' => $text];
                }else{
                    $white[] = ['info' => $text];
                }
            }
            elseif($update->getNewNextAired() === null and $update->getSerie()->getStatus() === "Continuing" and $update->getOldStatus() !== "Ended" and $update->getOldStatus() !== null){
                $orange = ['info' => $name." est en pause"];
            }
            elseif($update->getNewStatus() === "Upcoming"){
                $text = "La prochaine saison de ".$name." a été annoncée";

                if($update->getNewNextAired()){
                    $text .= " pour ".str_replace("en ", "", $this->timeService->dateUpcoming($update->getNewNextAired(), $update->getNextAiredType()));
                }

                $green[] = ['info' => $text];
            }
            elseif($update->getNewStatus() === null and $update->getOldStatus() === null and $update->getOldNextAired() and $update->getNewNextAired() === null){
                $red[] = ['info' => $name." est terminé pour l'instant"];
            }

        }


        $updates = [
            'green' => $green,
            'orange' => $orange,
            'red' => $red,
            'white' => $white,
        ];

        $this->mailService->sendEmail($updates);

        return Command::SUCCESS;
    }
}
