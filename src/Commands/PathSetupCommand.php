<?php

namespace SparkPhp\Installer\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PathSetupCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('path:setup')
            ->setDescription('Verifica e configura o PATH do Composer global no sistema')
            ->addOption('yes', 'y', InputOption::VALUE_NONE, 'Confirma automaticamente sem perguntar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (PHP_OS_FAMILY === 'Windows') {
            $this->showWindowsInstructions($io);
            return Command::SUCCESS;
        }

        $binDir = $this->getComposerBinDir();

        if ($this->isInPath($binDir)) {
            $io->success('PATH já está configurado corretamente.');
            return Command::SUCCESS;
        }

        $io->warning("O diretório do Composer global não está no seu PATH:\n  {$binDir}");

        $autoYes      = $input->getOption('yes');
        $interactive  = $input->isInteractive();

        if (!$autoYes && $interactive && !$io->confirm('Deseja configurar o PATH automaticamente?', true)) {
            $this->showManualInstructions($io, $binDir);
            return Command::SUCCESS;
        }

        if (!$autoYes && !$interactive) {
            // Rodando via post-install-cmd não-interativo — apenas informa
            $this->showManualInstructions($io, $binDir);
            return Command::SUCCESS;
        }

        $configFile = $this->detectShellConfig();

        if ($configFile === null) {
            $io->error('Shell não reconhecido. Configure o PATH manualmente.');
            $this->showManualInstructions($io, $binDir);
            return Command::FAILURE;
        }

        $this->appendToFile($configFile, $binDir);

        $io->success("PATH configurado em {$configFile}");
        $io->text('Para aplicar agora, execute:');
        $io->text("  <info>source {$configFile}</info>");
        $io->newLine();

        return Command::SUCCESS;
    }

    private function getComposerBinDir(): string
    {
        exec('composer global config bin-dir --absolute 2>/dev/null', $output, $code);

        if ($code === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        $home = $_SERVER['HOME'] ?? getenv('HOME') ?: '~';

        // Tenta os dois caminhos mais comuns
        foreach (["{$home}/.config/composer/vendor/bin", "{$home}/.composer/vendor/bin"] as $path) {
            if (is_dir($path)) {
                return $path;
            }
        }

        return "{$home}/.config/composer/vendor/bin";
    }

    private function isInPath(string $binDir): bool
    {
        $path = getenv('PATH') ?: '';

        // Normaliza ~ para o home real
        $home   = $_SERVER['HOME'] ?? getenv('HOME') ?: '';
        $binDir = str_replace('~', $home, $binDir);

        return str_contains($path, $binDir);
    }

    private function detectShellConfig(): ?string
    {
        $home  = $_SERVER['HOME'] ?? getenv('HOME') ?: '~';
        $shell = basename(getenv('SHELL') ?: '');

        return match ($shell) {
            'zsh'  => "{$home}/.zshrc",
            'fish' => "{$home}/.config/fish/config.fish",
            'bash' => file_exists("{$home}/.bash_profile")
                        ? "{$home}/.bash_profile"
                        : "{$home}/.bashrc",
            default => file_exists("{$home}/.bashrc")
                        ? "{$home}/.bashrc"
                        : (file_exists("{$home}/.profile") ? "{$home}/.profile" : null),
        };
    }

    private function appendToFile(string $configFile, string $binDir): void
    {
        $shell = basename(getenv('SHELL') ?: 'bash');

        $line = $shell === 'fish'
            ? "\n# SparkPHP Installer\nfish_add_path \"{$binDir}\"\n"
            : "\n# SparkPHP Installer\nexport PATH=\"{$binDir}:\$PATH\"\n";

        file_put_contents($configFile, $line, FILE_APPEND);
    }

    private function showManualInstructions(SymfonyStyle $io, string $binDir): void
    {
        $io->text('Adicione manualmente ao seu <comment>~/.bashrc</comment> ou <comment>~/.zshrc</comment>:');
        $io->newLine();
        $io->text("  <info>export PATH=\"{$binDir}:\$PATH\"</info>");
        $io->newLine();
        $io->text('Depois execute:');
        $io->text('  <info>source ~/.bashrc</info>');
        $io->newLine();
    }

    private function showWindowsInstructions(SymfonyStyle $io): void
    {
        exec('composer global config bin-dir --absolute 2>NUL', $output, $code);
        $binDir = ($code === 0 && !empty($output[0]))
            ? trim($output[0])
            : '%APPDATA%\Composer\vendor\bin';

        $io->text('No Windows, adicione o diretório ao PATH do sistema:');
        $io->newLine();
        $io->text("  <info>{$binDir}</info>");
        $io->newLine();
        $io->text('Via PowerShell (como Administrador):');
        $io->text("  <info>[Environment]::SetEnvironmentVariable('Path', \$env:Path + ';{$binDir}', 'User')</info>");
        $io->newLine();
    }
}
