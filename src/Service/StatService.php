<?php

namespace App\Service;

class StatService
{


    /**
     * @param array<string, mixed> $periods
     *
     * @return array<string, mixed>
     */
    public function initializeByPeriod(array $periods): array
    {

        return array_fill_keys($periods, 0);
    }


    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $counts
     *
     * @return array<string, mixed>
     */
    function divideByPeriod(array $data, array $counts): array
    {

        foreach ($data as $key => $value) {
            if ($counts[$key] > 0) {
                $data[$key] /= $counts[$key];
            }
        }

        return $data;
    }


    /**
     * @param array<int> $data
     *
     * @return string
     */
    function buildChart(array $data): string
    {

        return '['.implode(', ', $data).']';
    }


    /**
     * @param array<int, mixed> $data
     *
     * @return string[]
     */
    function buildLabelAndDataChart(array $data): array
    {

        $labels = array_map(fn($item) => '"'.$item['name'].'"', $data);
        $values = array_column($data, 'COUNT');
        return [
            'labels' => '['.implode(', ', $labels).']',
            'values' => '['.implode(', ', $values).']'
        ];
    }


    function removeAccent(string $str): string
    {

        $str = strtr($str, 'ÁÀÂÄÃÅÇÉÈÊËÍÏÎÌÑÓÒÔÖÕÚÙÛÜÝ', 'AAAAAACEEEEEIIIINOOOOOUUUUY');
        return strtr($str, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ', 'aaaaaaceeeeiiiinooooouuuuyy');
    }

}