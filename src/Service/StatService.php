<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use JetBrains\PhpStorm\ArrayShape;

class StatService
{

    private ObjectManager $manager;


    public function __construct(ManagerRegistry $managerRegistry)
    {

        $this->manager = $managerRegistry->getManager();
    }


    public function initializeByPeriod(array $periods): array
    {

        return array_fill_keys($periods, 0);
    }


    function divideByPeriod(&$data, $counts)
    {

        foreach ($data as $key => $value) {
            if ($counts[$key] > 0) {
                $data[$key] /= $counts[$key];
            }
        }

        return $data;
    }


    function buildChart($data): string
    {

        return '['.implode(', ', $data).']';
    }


    #[ArrayShape(['labels' => "string", 'values' => "string"])]
    function buildLabelAndDataChart($data): array
    {

        $labels = array_map(fn($item) => '"'.$item['name'].'"', $data);
        $values = array_column($data, 'COUNT');
        return [
            'labels' => '['.implode(', ', $labels).']',
            'values' => '['.implode(', ', $values).']'
        ];
    }


    function removeAccent($str): string
    {

        $str = strtr($str, 'ÁÀÂÄÃÅÇÉÈÊËÍÏÎÌÑÓÒÔÖÕÚÙÛÜÝ', 'AAAAAACEEEEEIIIINOOOOOUUUUY');
        return strtr($str, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ', 'aaaaaaceeeeiiiinooooouuuuyy');
    }

}