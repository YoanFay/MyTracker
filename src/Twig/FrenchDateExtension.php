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
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
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
}