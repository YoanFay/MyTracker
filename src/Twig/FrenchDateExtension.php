<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use DateTime;

class FrenchDateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
// If your filter generates SAFE HTML, you should add a third
// parameter: ['is_safe' => ['html']]
// Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('dateF', [$this, 'frenchFormatDate']),
            new TwigFilter('dateFNoDay', [$this, 'frenchFormatDateNoDay']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dateUpcoming', [$this, 'dateUpcoming']),
        ];
    }

    public function dateUpcoming($date, $type = 'day'){

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        // Jour de la semaine
        $joursSemaine = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
        $day = $joursSemaine[$date->format('w')];

        // Mois
        $moisEnFrancais = array(
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
            'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        );

        $month = $moisEnFrancais[$date->format('n') - 1];

        $year = $date->format('Y');

        // Numéro du jour avec suffixe (1er, 2e, etc.)
        $numeroJour = $date->format('j');
        $suffixe = ($numeroJour == 1) ? 'er' : '';

        return match ($type){
            'year' => $year,
            'month' => $month." ".$year,
            'day' => 'le '.$day.$suffixe." ".$month." ".$year
        };

    }

    public function frenchFormatDate($date): string
    {
        
        if (is_string($date)) {
            $date = new DateTime($date);
        }
        
    // Jour de la semaine
    $joursSemaine = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
    $jourSemaine = $joursSemaine[$date->format('w')];

    // Numéro du jour avec suffixe (1er, 2e, etc.)
    $numeroJour = $date->format('j');
    $suffixe = ($numeroJour == 1) ? 'er' : '';

    // Mois
    $moisEnFrancais = array(
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
        'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    );
    $mois = $moisEnFrancais[$date->format('n') - 1];

    // Année
    $annee = $date->format('Y');

    // Affichage de la date
    return $jourSemaine." ".$numeroJour.$suffixe." ".$mois." ".$annee;
    }

    public function frenchFormatDateNoDay($date): string
    {
        
        if (is_string($date)) {
            $date = new DateTime($date);
        }

    // Numéro du jour avec suffixe (1er, 2e, etc.)
    $numeroJour = $date->format('j');
    $suffixe = ($numeroJour == 1) ? 'er' : '';

    // Mois
    $moisEnFrancais = array(
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
        'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    );
    $mois = $moisEnFrancais[$date->format('n') - 1];

    // Année
    $annee = $date->format('Y');

    // Affichage de la date
    return $numeroJour.$suffixe." ".$mois." ".$annee;
    }
}