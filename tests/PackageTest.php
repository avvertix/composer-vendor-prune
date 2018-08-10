<?php

namespace Avvertix\VendorPrune\Tests;

use Illuminate\Support\Collection;
use Avvertix\VendorPrune\Support\Package;
use Avvertix\VendorPrune\Support\Formatter;
use PHPUnit\Framework\TestCase as BaseTestCase;

class PackageTest extends TestCase
{

    public function test_packages_are_loaded_from_installed_json_of_the_project()
    {
        $packages = Package::load(__DIR__.'/../vendor');

        $this->assertInstanceOf(Collection::class, $packages);
        $this->assertContainsOnlyInstancesOf(Package::class, $packages);
    }

    public function test_packages_are_loaded_from_installed_json()
    {
        $packages = Package::load(__DIR__.'/fixture/vendor');

        $this->assertInstanceOf(Collection::class, $packages);
        $this->assertContainsOnlyInstancesOf(Package::class, $packages);
        $this->assertEquals(1, $packages->count());
        $this->assertEquals('my/package', $packages->first()->name);
    }

    public function test_package_size_is_calculated()
    {
        $filesInPackage = [
            __DIR__.'/fixture/vendor/my/package/composer.json',
            __DIR__.'/fixture/vendor/my/package/readme.md',
            __DIR__.'/fixture/vendor/my/package/Source.php',
            __DIR__.'/fixture/vendor/my/package/LICENSE',
        ];

        $expectedSize = 0;

        foreach ($filesInPackage as $file) {
            $expectedSize += filesize($file);
        }

        $package = Package::load(__DIR__.'/fixture/vendor')->first();

        $this->assertEquals('my/package', $package->name);
        $this->assertEquals($expectedSize, $package->size());
    }


    public function test_prune_dry_run_give_some_expectations()
    {
        $package = Package::load(__DIR__.'/fixture/vendor')->first();
        
        $expectedFilesToPrune = [
            __DIR__.'/fixture/vendor/my/package/readme.md',
        ];
        
        $expectedPruneSize = 0;
        
        foreach ($expectedFilesToPrune as $file) {
            $expectedPruneSize += filesize($file);
        }

        list($testPruneSize, $testPruneCount, $testPruneFiles, $testPrune) = $package->pruneDryRun();

        $this->assertEquals(1, $testPruneCount);
        $this->assertEquals($expectedPruneSize, $testPruneSize);
        $this->assertEquals(realpath($expectedFilesToPrune[0]), realpath($testPruneFiles->keys()->first()));

        $expectedGainString = Formatter::gain($package->size(), $expectedPruneSize);
        $this->assertEquals($expectedGainString, $testPrune);
    }

    public function test_package_prune()
    {
        @\unlink(__DIR__.'/fixture/prunable-vendor');
        $this->copyDirectory(__DIR__.'/fixture/vendor', __DIR__.'/fixture/prunable-vendor');

        $package = Package::load(__DIR__.'/fixture/prunable-vendor')->first();
        
        $expectedPruneSize =0;
        $expectedFilesToPrune = [
            __DIR__.'/fixture/prunable-vendor/my/package/readme.md',
        ];

        foreach ($expectedFilesToPrune as $file) {
            $expectedPruneSize += filesize($file);
        }

        
        list($testPruneSize, $testPruneCount, $testPruneFiles, $testPrune) = $package->prune();

        $this->assertEquals(1, $testPruneCount);
        $this->assertEquals($expectedPruneSize, $testPruneSize);
        $this->assertEquals(realpath($expectedFilesToPrune[0]), realpath($testPruneFiles->keys()->first()));
        $this->assertFalse(is_file($expectedFilesToPrune[0]), 'Expected file not removed');

        $expectedGainString = Formatter::gain($package->size(), $expectedPruneSize);
        $this->assertEquals($expectedGainString, $testPrune);
    }

}
