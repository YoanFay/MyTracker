<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use DateTime;

class MsToHM extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
// If your filter generates SAFE HTML, you should add a third
// parameter: ['is_safe' => ['html']]
// Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('MsToHM', [$this, 'convertirMillisecondesEnHeureMinute']),
            new TwigFilter('MsToMS', [$this, 'convertirMillisecondesEnMinutesSecondes']),
            new TwigFilter('SToHM', [$this, 'convertirSecondesEnHeureMinute']),
        ];
    }

    public function convertirMillisecondesEnHeureMinute(int $millisecondes): string
    {
        // Convertir les millisecondes en secondes
        $secondes = $millisecondes / 1000;

        // Calculer les heures, minutes et secondes
        $heures = floor($secondes / 3600);
        $minutes = floor(($secondes % 3600) / 60);

        if($minutes < 10){
            $minutes = '0'.$minutes;
        }

        // Formater le résultat
        $formatHeureMinute = $heures."h".$minutes;

        return $formatHeureMinute;
    }

    public function convertirMillisecondesEnMinutesSecondes(int $millisecondes): string
    {
        $secondes = $millisecondes / 1000;
        $minutes = floor($secondes / 60);

        // Reste des minutes après la conversion en heures
        $secondes %= 60;

        if ($secondes === 0){
            return $minutes.'s';
        }elseif ($secondes < 10){
            return $minutes.'min0'.$secondes;
        }

        return $minutes.'min'.$secondes;

    }

    public function convertirSecondesEnHeureMinute(int $secondes): string
    {

        // Calculer les heures, minutes et secondes
        $heures = floor($secondes / 3600);
        $minutes = floor(($secondes % 3600) / 60);

        if($minutes < 10){
            $minutes = '0'.$minutes;
        }

        // Formater le résultat
        $formatHeureMinute = $heures."h";

        if ($minutes > 0){
            $formatHeureMinute .= $minutes;
        }

        return $formatHeureMinute;
    }
}