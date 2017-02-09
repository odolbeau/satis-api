<?php

namespace Bab\SatisApi;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Builder
{
    private $rootDir;
    private $outputDir;
    private $configFile;

    public function __construct(string $rootDir, string $outputDir, string $configFile)
    {
        $this->rootDir = $rootDir;
        $this->outputDir = $outputDir;
        $this->configFile = $configFile;
    }

    public function buildRepository($repositoryUrl)
    {
        $command = $this->rootDir.'/vendor/bin/satis build '.$this->configFile.' '.$this->outputDir." --repository-url=$repositoryUrl";
        echo "$command\n";
        $process = new Process($command);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }
}
