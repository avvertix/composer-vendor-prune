<?php

namespace Avvertix\VendorPrune\Tests;

use Illuminate\Support\Collection;
use Avvertix\VendorPrune\Support\Package;
use Avvertix\VendorPrune\Support\Formatter;

class PruneCommandTest extends TestCase
{

    public function test_prune_command_dry_run()
    {
        $exitCode = $this->executeCommand('prune', ['--dry-run' => true]);
        $output = $this->output();
        
        $this->assertEquals(0, $exitCode);
        $this->assertContains('symfony/console 882.97KB => 22.67KB 2.57%', $output);
    }

    public function test_prune_command_dry_run_using_custom_vendor_folder()
    {
        @\unlink(__DIR__.'/fixture/prunable-vendor');
        $this->copyDirectory(__DIR__.'/fixture/vendor', __DIR__.'/fixture/prunable-vendor');

        $exitCode = $this->executeCommand('prune', ['--dry-run' => true,
        '--vendor-folder' => __DIR__.'/fixture/prunable-vendor']);
        $output = $this->output();
        
        $this->assertEquals(0, $exitCode);
        $this->assertContains('my/package', $output);
        $this->assertTrue(is_file(__DIR__.'/fixture/prunable-vendor/my/package/readme.md'), 'The file was deleted, even if was a dry run');
    }

    public function test_prune_command_run_using_custom_vendor_folder()
    {
        @\unlink(__DIR__.'/fixture/prunable-vendor');
        $this->copyDirectory(__DIR__.'/fixture/vendor', __DIR__.'/fixture/prunable-vendor');

        $exitCode = $this->executeCommand('prune', ['--vendor-folder' => __DIR__.'/fixture/prunable-vendor']);
        $output = $this->output();
        
        $expectedFilesToPrune = [
            __DIR__.'/fixture/prunable-vendor/my/package/readme.md',
        ];

        $this->assertEquals(0, $exitCode);
        $this->assertContains('my/package', $output);
        $this->assertFalse(is_file($expectedFilesToPrune[0]), 'Expected file not removed');
    }

}
