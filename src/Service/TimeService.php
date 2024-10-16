<?php

namespace App\Service;

use DateTime;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TimeService
{
    
    public function convertirMillisecondes($millisecondes) {
        // Calcul des heures, minutes et secondes
        $secondes = floor($millisecondes / 1000);
        $minutes = floor($secondes / 60);
        $heures = floor($minutes / 60);
        
        // Reste des secondes après la conversion en minutes
        $secondes %= 60;
        
        // Reste des minutes après la conversion en heures
        $minutes %= 60;
        
        return $heures.'h'.$minutes.'min';
    }
    
    public function convertirSecondes($secondes) {
        $minutes = floor($secondes / 60);
        $heures = floor($minutes / 60);
        
        // Reste des secondes après la conversion en minutes
        $secondes %= 60;
        
        // Reste des minutes après la conversion en heures
        $minutes %= 60;

        if ($minutes === 0){
            return $heures.'h';
        }elseif ($minutes < 10){
            return $heures.'h0'.$minutes;
        }
        
        return $heures.'h'.$minutes;
    }
    
    public function convertirHeureMinute(?int $heures = 0, ?int $minutes = 0) {
        $minutes += $heures * 60;

        $secondes = $minutes * 60;

        if($secondes === 0){
            $secondes = null;
        }
        
        return $secondes;
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
}
