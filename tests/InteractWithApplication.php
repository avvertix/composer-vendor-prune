<?php

namespace Avvertix\VendorPrune\Tests;

use Avvertix\VendorPrune\Commands\PruneCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;

trait InteractWithApplication
{
    protected $application;

    protected $lastRun;

    /**
     * Execute the given command
     * 
     * @param string $name the command name
     * @param array $input the input to pass to the command
     * @return integer the command exit code
     */
    protected function executeCommand($name, $input = [])
    {
        $application = new Application();
        $pruneCommand = new PruneCommand();
        $application->add($pruneCommand);

        $command = $application->find($name);
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute(array_merge(
            ['command'  => $command->getName()],
            $input));

        $this->lastRun = $commandTester->getDisplay();
        
        return $exitCode;
    }

    /**
     * Retrieve the output of the last executed command
     * 
     * @return string
     */
    protected function output()
    {
        return $this->lastRun;
    }
}
