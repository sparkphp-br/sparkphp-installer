<?php

namespace SparkPhp\Installer;

use Symfony\Component\Console\Application as SymfonyApplication;
use SparkPhp\Installer\Commands\NewCommand;

class Application
{
    public function run(): void
    {
        $app = new SymfonyApplication('SparkPHP Installer', '1.0.0');
        $app->add(new NewCommand());
        $app->setDefaultCommand('new', false);
        $app->run();
    }
}
