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
            new TwigFilter('SToHM', [$this, 'convertirSecondesEnHeureMinute']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    public function convertirMillisecondesEnHeureMinute($millisecondes) {
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

    public function convertirSecondesEnHeureMinute($secondes) {

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