<?php

namespace SparkPhp\Installer;

use SparkPhp\Installer\Commands\NewCommand;
use SparkPhp\Installer\Commands\PathSetupCommand;
use SparkPhp\Installer\Commands\SelfUpdateCommand;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application
{
    public const VERSION = '1.0.0';

    public function run(): void
    {
        $app = new SymfonyApplication('SparkPHP Installer', self::VERSION);

        $app->add(new NewCommand());
        $app->add(new SelfUpdateCommand());
        $app->add(new PathSetupCommand());
        $app->setDefaultCommand('new', false);

        $app->run();
    }
}
