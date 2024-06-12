<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use DateTime;

class Age extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
// If your filter generates SAFE HTML, you should add a third
// parameter: ['is_safe' => ['html']]
// Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('age', [$this, 'calculAge']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    public function calculAge($date) {

        $today = new DateTime();

        $today->setTime(0, 0);

        $age = $today->format('Y') - $date->format('Y');

        $date->setDate('2024', $date->format('m'), $date->format('d'));

        if($date >= $today){
            $age--;
        }

        return $age;
    }
}