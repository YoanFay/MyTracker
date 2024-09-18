<?php

namespace App\Twig;

use App\Entity\Serie;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use DateTime;

class Image extends AbstractExtension
{

    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('infoImage', [$this, 'infoImage']),
        ];
    }

    public function infoImage(Serie $serie, $env) {

        $height = null;
        $width = 400;

        if ($serie->getArtwork() && file_exists($this->kernel->getProjectDir().$serie->getArtwork()->getPath())){

            $path = $serie->getArtwork()->getPath();

            if ($env === "dev"){
                $path = str_replace('public/', '',$path);
            }

            $artwork = $serie->getArtwork();

            if ($artwork && $artwork->getPath() && $artwork->getHeight() && $artwork->getWidth() && $artwork->getWidth() > 0){

                if($width > $artwork->getWidth()){
                    $width = $artwork->getWidth();
                }

                $height = ($width * $artwork->getHeight()) / $artwork->getWidth();
            }

            return [
                'path' => $path,
                'alt' => $serie->getName()." poster",
                'width' => $width,
                'height' => $height
            ];
        }

        $basePath = "/image/visuel-a-venir.jpg";

        $size = getimagesize($this->kernel->getProjectDir()."/public".$basePath);

        $artworkHeight = $size[1];
        $artworkWidth = $size[0];

        $height = (400 * $artworkHeight) / $artworkWidth;

        return [
            'path' => $basePath,
            'alt' => "Visuel à venir",
            'width' => $width,
            'height' => $height
        ];

    }
}