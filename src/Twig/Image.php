<?php

namespace App\Twig;

use App\Entity\Game;
use App\Entity\Manga;
use App\Entity\MangaTome;
use App\Entity\Music;
use App\Entity\Serie;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

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
            new TwigFunction('infoImageMovie', [$this, 'infoImageMovie']),
            new TwigFunction('infoImageManga', [$this, 'infoImageManga']),
            new TwigFunction('infoImageMangaTome', [$this, 'infoImageMangaTome']),
            new TwigFunction('infoGame', [$this, 'infoGame']),
            new TwigFunction('infoImageMusic', [$this, 'infoImageMusic']),
        ];
    }


    /**
     * @param Serie  $serie
     * @param string $env
     *
     * @return array<string, mixed>
     */
    public function infoImage(Serie $serie, string $env): array
    {

        $height = null;
        $width = 400;

        if ($serie->getArtwork() && file_exists($this->kernel->getProjectDir().$serie->getArtwork()->getPath())) {

            /** @var string $path */
            $path = $serie->getArtwork()->getPath();

            if ($env === "dev") {
                $path = str_replace('public/', '', $path);
            }

            $artwork = $serie->getArtwork();

            if ($artwork->getPath() && $artwork->getHeight() && $artwork->getWidth() && $artwork->getWidth() > 0) {

                if ($width > $artwork->getWidth()) {
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

        if ($size) {
            $artworkHeight = $size[1];
            $artworkWidth = $size[0];

            $height = (400 * $artworkHeight) / $artworkWidth;
        }

        return [
            'path' => $basePath,
            'alt' => "Visuel à venir",
            'width' => $width,
            'height' => $height
        ];

    }


    /**
     * @param string $path
     * @param string $name
     * @param string $env
     *
     * @return array<string, string>
     */
    public function infoImageMovie(string $path, string $name, string $env): array
    {

        if ($path && file_exists($this->kernel->getProjectDir().$path)) {

            if ($env === "dev") {
                $path = str_replace('public/', '', $path);
            }

            return [
                'path' => $path,
                'alt' => $name." poster"
            ];

        }

        return [
            'path' => "/image/visuel-a-venir.jpg",
            'alt' => "Visuel à venir"
        ];

    }


    /**
     * @param Manga  $manga
     * @param string $path
     * @param string $env
     *
     * @return array<string, string>
     */
    public function infoImageManga(Manga $manga, string $path, string $env): array
    {

        if ($path && file_exists($this->kernel->getProjectDir().$path)) {

            if ($env === "dev") {
                $path = str_replace('public/', '', $path);
            }

            return [
                'path' => $path,
                'alt' => $manga->getName()." poster"
            ];

        }

        return [
            'path' => "/image/visuel-a-venir.jpg",
            'alt' => "Visuel à venir"
        ];

    }


    /**
     * @param MangaTome $mangaTome
     * @param string    $env
     *
     * @return array<string, string>
     */
    public function infoImageMangaTome(MangaTome $mangaTome, string $env): array
    {

        $path = $mangaTome->getCover();

        if ($path && file_exists($this->kernel->getProjectDir().$path)) {

            if ($env === "dev") {
                $path = str_replace('public/', '', $path);
            }

            return [
                'path' => $path,
                'alt' => $mangaTome->getTomeNumber()." poster"
            ];

        }

        return [
            'path' => "/image/visuel-a-venir.jpg",
            'alt' => "Visuel à venir"
        ];

    }


    /**
     * @param Game   $game
     * @param string $env
     *
     * @return array<string, string>
     */
    public function infoGame(Game $game, string $env): array
    {

        $path = $game->getCover();

        if ($path && file_exists($this->kernel->getProjectDir().$path)) {

            if ($env === "dev") {
                $path = str_replace('public/', '', $path);
            }

            return [
                'path' => $path,
                'alt' => $game->getName()." poster"
            ];

        }

        return [
            'path' => "/image/visuel-a-venir.jpg",
            'alt' => "Visuel à venir"
        ];

    }


    /**
     * @param Music  $music
     * @param string $env
     *
     * @return array<string, string>
     */
    public function infoImageMusic(Music $music, string $env): array
    {

        $path = "/public/image/music/cover/".$music->getId().".jpeg";

        if ($path && file_exists($this->kernel->getProjectDir().$path)) {

            if ($env === "dev") {
                $path = str_replace('public/', '', $path);
            }

            return [
                'path' => $path,
                'alt' => $music->getName()."by".$music->getMusicArtist()->getName()." cover"
            ];

        }

        return [
            'path' => "/image/visuel-a-venir.jpg",
            'alt' => "Visuel à venir"
        ];

    }
}