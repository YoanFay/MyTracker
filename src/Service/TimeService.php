<?php

namespace App\Service;

use DateTime;
use Exception;

class TimeService
{
    
    public function convertirSecondes(int $secondes): string
    {
        $minutes = floor($secondes / 60);
        $heures = floor($minutes / 60);
        
        // Reste des minutes après la conversion en heures
        $minutes %= 60;

        if ($minutes === 0){
            return $heures.'h';
        }elseif ($minutes < 10){
            return $heures.'h0'.$minutes;
        }
        
        return $heures.'h'.$minutes;
    }
    
    public function convertirHeureMinute(?int $heures = 0, ?int $minutes = 0): float|int|null
    {
        $minutes += $heures * 60;

        $secondes = $minutes * 60;

        if($secondes === 0){
            $secondes = null;
        }
        
        return $secondes;
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
    public function dateUpcoming(mixed $date, string $type): string
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
}
