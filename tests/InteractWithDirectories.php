<?php

namespace Avvertix\VendorPrune\Tests;

use Symfony\Component\Finder\Finder as SymfonyFinder;

trait InteractWithDirectories
{
    protected function copyDirectory($source, $destination)
    {
        $finder = new SymfonyFinder();
        $finder->files()->in($source)->ignoreVCS(false);

        $sourceFile = null;
        $destinationFile = null;
        $destinationDir = rtrim($destination, '/');

        foreach ($finder as $file) {

            $destinationFile = "$destinationDir/{$file->getRelativePathname()}";
            
            if(!is_dir(dirname($destinationFile))){
// dump(compact('file', 'destinationFile'));
                // getRelativePath
                mkdir(dirname($destinationFile), 0777, true);
            }

            copy($file, $destinationFile);
        }

        
    }
}
