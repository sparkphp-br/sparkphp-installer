<?php

namespace SparkPhp\Installer\Commands;

use SparkPhp\Installer\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WelcomeCommand extends Command
{
    private const PACKAGIST_API = 'https://repo.packagist.org/p2/sparkphp-br/installer.json';

    protected function configure(): void
    {
        $this
            ->setName('welcome')
            ->setDescription('Menu interativo do SparkPHP Installer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->printHeader($io);

        $hasUpdate    = false;
        $latestVersion = $this->fetchLatestVersion();

        if ($latestVersion !== null && version_compare($latestVersion, Application::VERSION, '>')) {
            $hasUpdate = true;
            $io->writeln("  <fg=yellow>↑ Nova versão disponível: <options=bold>v{$latestVersion}</></>");
        } else {
            $io->writeln('  <fg=green>✓ Você está na versão mais recente</>');
        }

        $io->newLine();

        $options = ['Criar novo projeto'];

        if ($hasUpdate) {
            $options[] = 'Atualizar installer para v' . $latestVersion;
        }

        $options[] = 'Sair';

        $choice = $io->choice('O que deseja fazer?', $options, $options[0]);

        $io->newLine();

        if (str_starts_with($choice, 'Criar novo projeto')) {
            return $this->runCommand('new', $input, $output);
        }

        if (str_starts_with($choice, 'Atualizar')) {
            return $this->runCommand('self-update', $input, $output);
        }

        return Command::SUCCESS;
    }

    private function printHeader(SymfonyStyle $io): void
    {
        $io->newLine();
        $io->writeln('  <fg=cyan;options=bold>⚡ SparkPHP Installer</>  <fg=gray>v' . Application::VERSION . '</>');
        $io->newLine();
    }

    private function runCommand(string $name, InputInterface $input, OutputInterface $output): int
    {
        $command  = $this->getApplication()->find($name);
        $newInput = new ArrayInput([]);
        $newInput->setInteractive($input->isInteractive());

        return $command->run($newInput, $output);
    }

    private function fetchLatestVersion(): ?string
    {
        $context = stream_context_create([
            'http' => [
                'timeout'    => 3,
                'user_agent' => 'sparkphp-installer/' . Application::VERSION,
            ],
            'ssl' => [
                'verify_peer' => true,
            ],
        ]);

        $json = @file_get_contents(self::PACKAGIST_API, false, $context);

        if ($json === false) {
            return null;
        }

        $data = json_decode($json, true);

        $packages = $data['packages']['sparkphp-br/installer'] ?? [];

        $stable = array_filter(
            array_column($packages, 'version'),
            fn(string $v) => preg_match('/^v?\d+\.\d+\.\d+$/', $v)
        );

        if (empty($stable)) {
            return null;
        }

        usort($stable, fn($a, $b) => version_compare(
            ltrim($b, 'v'),
            ltrim($a, 'v')
        ));

        return ltrim($stable[0], 'v');
    }
}
