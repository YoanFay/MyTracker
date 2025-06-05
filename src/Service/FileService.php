<?php

namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;

class FileService
{

    private KernelInterface $kernel;


    public function __construct(KernelInterface $kernel)
    {

        $this->kernel = $kernel;
    }


    public function addFile($link, $destinationFolder): bool
    {

        $fileContent = file_get_contents($link);

        if ($fileContent !== false) {
            $cover = imagecreatefromstring($fileContent);

            if ($cover) {
                $projectDir = $this->kernel->getProjectDir();

                if (imagejpeg($cover, $projectDir.$destinationFolder, 100)) {
                    return true;
                }
            }
        }

        return false;

    }
}
