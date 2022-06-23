<?php

namespace Avvertix\VendorPrune\Commands;

use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Avvertix\VendorPrune\Support\Package;
use Avvertix\VendorPrune\Support\Path;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PruneCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('prune')
            ->setDescription('Prune a Composer vendor folder.')
            ->setHelp('Clean the Composer vendor folder from unneeded files')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Test the prune giving some information before it is applied')
            ->addOption('vendor-folder', null, InputOption::VALUE_REQUIRED, 'The path to the vendor folder');
            // ->addArgument('package', InputArgument::OPTIONAL, 'The name of the package to prune. Not giving a name will cause all packages to be pruned');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pruneMethod = !!$input->getOption('dry-run') ? 'pruneDryRun' : 'prune';
        $vendorFolder = $input->getOption('vendor-folder') ?? Path::path('vendor');

        if(!is_dir($vendorFolder)){
            throw new InvalidArgumentException("Given vendor folder [$vendorFolder] does not exists");
        }

        $output->writeln("Pruning [$vendorFolder]...");
        
        $packages = Package::load($vendorFolder);
        
        foreach ($packages as $package) {
            if (!$package->isValid()) {
                $output->writeln("$package->name - SKIP Isn't valid");
                continue;
            }

            list($testPruneSize, $testPruneCount, $testPruneFiles, $testPrune) = $package->$pruneMethod();

            $output->writeln("$package->name $testPrune");
            if ($output->isVerbose()) {
                $output->writeln("- $testPruneCount files to prune");
                foreach ($testPruneFiles as $file) {
                    $output->writeln("-- $file");
                }
            }
        }

        if($packages->isEmpty()){
            $output->writeln("No packages to prune found in [$vendorFolder].");
        }
        
    }

}
