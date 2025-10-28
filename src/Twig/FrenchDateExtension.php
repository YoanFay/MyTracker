<?php

namespace App\Twig;

use Exception;
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
            new TwigFilter('dateFNoDayWithHour', [$this, 'frenchFormatDateNoDayWithHour']),
        ];
    }


    public function getFunctions(): array
    {

        return [
            new TwigFunction('dateUpcoming', [$this, 'dateUpcoming']),
        ];
    }


    public function dateUpcoming(mixed $date, ?string $type): string
    {

        if (!$type) {
            $type = "day";
        }

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
        $numDay = $date->format('j');
        $suffixe = ($numDay == 1) ?
            'er' :
            '';

        return match ($type) {
            'year' => 'en '.$year,
            'month' => 'en '.$month." ".$year,
            'day' => 'le '.$day." ".$numDay.$suffixe." ".$month." ".$year,
            default => throw new \InvalidArgumentException(sprintf('Le type "%s" n\'est pas supporté.', $type)),
        };

    }


    /**
     * @throws Exception
     */
    public function frenchFormatDate(mixed $date): string
    {

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        // Jour de la semaine
        $joursSemaine = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
        $jourSemaine = $joursSemaine[$date->format('w')];

        // Numéro du jour avec suffixe (1er, 2e, etc.)
        $numeroJour = $date->format('j');
        $suffixe = ($numeroJour == 1) ?
            'er' :
            '';

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


    /**
     * @throws Exception
     */
    public function frenchFormatDateNoDay(mixed $date): string
    {

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        // Numéro du jour avec suffixe (1er, 2e, etc.)
        $numeroJour = $date->format('j');
        $suffixe = ($numeroJour == 1) ?
            'er' :
            '';

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


    /**
     * @throws Exception
     */
    public function frenchFormatDateNoDayWithHour(mixed $date): string
    {

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        // Numéro du jour avec suffixe (1er, 2e, etc.)
        $numeroJour = $date->format('j');
        $suffixe = ($numeroJour == 1) ?
            'er' :
            '';

        // Mois
        $moisEnFrancais = array(
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
            'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        );
        $mois = $moisEnFrancais[$date->format('n') - 1];

        // Année
        $annee = $date->format('Y');

        // Affichage de la date
        return $numeroJour.$suffixe." ".$mois." ".$annee.' - '.$date->format('H').':'.$date->format('i');
    }
}