<?php

namespace Avvertix\VendorPrune\Support;

use InvalidArgumentException;
use Illuminate\Support\Fluent;
use Symfony\Component\Finder\Finder as SymfonyFinder;

final class Package extends Fluent
{
    /**
     * @var string the path on disk of the package
     */
    private $path;

    /**
     * @var integer calculated package size
     */
    private $size = null;

    /**
     * @var bool
     */
    private $isValid = false;

    /**
     * Create a new package instance.
     *
     * @param  string   $vendorPath the path to the vendor folder that contains the package
     * @param  array    $attributes the package attributes as gathered from the composer.json or installed.json
     * @return void
     */
    public function __construct($vendorPath, $attributes)
    {
        if(!isset($attributes['name'])){
            throw new InvalidArgumentException("Attributes do not contain a [name] attribute");
        }
        parent::__construct($attributes);
        $this->path = rtrim($vendorPath, '/') . "/$this->name";
        $this->isValid = isset($attributes['name']) && is_dir($this->path);
    }

    public function path()
    {
        return $this->path;
    }

    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * The size occupied by the package
     * 
     * @return integer the size in bytes
     */
    public function size()
    {
        if(!is_null($this->size)){
            return $this->size;
        }
        $finder = collect(tap(new SymfonyFinder(), function($symfonyFinder){
            $symfonyFinder->files()->in($this->path);
        }));

        return $this->size = $finder->sum->getSize();
    }


    /**
     * Load a package or a list of packages from the specified vendor folder
     * 
     * @param string $vendorPath the path of the vendor folder
     * @return Illuminate\Support\Collection<Package>
     */
    public static function load(string $vendorPath)
    {
        $path = rtrim($vendorPath, '/');

        $packages = collect();

        if(is_file("$path/composer/installed.json")){
            $installed = json_decode(\file_get_contents("$path/composer/installed.json"), true);
            $packages = $packages->merge(isset($installed['packages']) ? $installed['packages'] : $installed);
        }

        return $packages->map(function($p) use($vendorPath) {
            return new static($vendorPath, $p);
        });
    }

    public function prune($options = [])
    {
        $dryRunReturn = $this->pruneDryRun();

        $files = $dryRunReturn[2];

        if(!empty($files)){
            foreach ($files as $file) {
                @unlink($file);
            }
        }

        return $dryRunReturn;
    }

    /**
     * Make a prune test run. Will give the expectations after pruning
     */
    public function pruneDryRun($options = [])
    {
        $finder = new SymfonyFinder();
        $finder->files()
               ->in($this->path)
               ->name('*.md')
               ->name('*.dist')
               ->ignoreVCS(false);

        $filesToPrune = collect($finder);
        
        $size = $filesToPrune->sum->getSize();
        $count = $filesToPrune->count();

        return [$size, $count, $filesToPrune, Formatter::gain($this->size(), $size)];
    }


    public function __toString()
    {
        return $this->name . ' ' . Formatter::size($this->size());
    }
}
