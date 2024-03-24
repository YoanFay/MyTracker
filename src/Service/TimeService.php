<?php

namespace App\Service;

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
}
