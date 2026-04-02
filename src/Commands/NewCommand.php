<?php

namespace SparkPhp\Installer\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NewCommand extends Command
{
    private const REQUIRED_PHP = '8.3.0';
    private const SKELETON     = 'sparkphp-br/sparkphp';

    protected function configure(): void
    {
        $this
            ->setName('new')
            ->setDescription('Cria um novo projeto SparkPHP')
            ->addArgument('name', InputArgument::OPTIONAL, 'Nome do projeto')
            ->addOption('no-docs', null, InputOption::VALUE_NONE, 'Cria o projeto sem a documentação')
            ->addOption('git',     null, InputOption::VALUE_NONE, 'Inicializa repositório Git após a criação')
            ->addOption('no-git',  null, InputOption::VALUE_NONE, 'Não inicializa repositório Git');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io          = new SymfonyStyle($input, $output);
        $interactive = $input->isInteractive();
        $name        = $input->getArgument('name');

        if (!$name) {
            if (!$interactive) {
                $io->error('Informe o nome do projeto: sparkphp new <nome>');
                return Command::FAILURE;
            }
            $name = $io->ask('Nome do projeto');
            if (!$name) {
                $io->error('O nome do projeto não pode ser vazio.');
                return Command::FAILURE;
            }
        }

        $io->title('SparkPHP Installer');

        // PHP version check
        if (version_compare(PHP_VERSION, self::REQUIRED_PHP, '<')) {
            $io->error(sprintf(
                'PHP >= %s é necessário. Versão atual: %s',
                self::REQUIRED_PHP,
                PHP_VERSION
            ));
            return Command::FAILURE;
        }

        // Composer check
        if (!$this->commandExists('composer')) {
            $io->error('Composer não encontrado. Instale em https://getcomposer.org');
            return Command::FAILURE;
        }

        // Directory check
        if (is_dir($name)) {
            $io->error("O diretório '{$name}' já existe.");
            return Command::FAILURE;
        }

        // Resolve options interactively when flags not provided
        $noDocs  = $input->getOption('no-docs');
        $withGit = $input->getOption('git');
        $noGit   = $input->getOption('no-git');

        if (!$noDocs && $interactive) {
            $noDocs = !$io->confirm('Incluir documentação no projeto?', true);
        }

        if (!$withGit && !$noGit && $interactive && $this->commandExists('git')) {
            $withGit = $io->confirm('Inicializar repositório Git?', true);
        }

        // Create project
        $io->text("Criando projeto <info>{$name}</info>...");
        $io->newLine();

        passthru(
            sprintf('composer create-project %s %s', self::SKELETON, escapeshellarg($name)),
            $result
        );

        if ($result !== 0) {
            $io->error('Falha ao criar o projeto.');
            return Command::FAILURE;
        }

        // Remove docs if requested
        if ($noDocs) {
            $projectPath = realpath($name);

            if ($projectPath !== false) {
                $this->removeDir($projectPath . '/docs');
                $this->removeDir($projectPath . '/app/views/docs');
                $this->removeDir($projectPath . '/app/views/exemplos');
                $io->text('<comment>Documentação removida.</comment>');
            }
        }

        // Git init
        if ($withGit && $this->commandExists('git')) {
            $io->newLine();
            $io->text('Inicializando repositório Git...');
            exec("git -C " . escapeshellarg($name) . " init && git -C " . escapeshellarg($name) . " add -A && git -C " . escapeshellarg($name) . " commit -m 'chore: initial commit'", result_code: $gitResult);

            if ($gitResult === 0) {
                $io->text('<info>Repositório Git inicializado.</info>');
            }
        }

        // Success
        $io->newLine();
        $io->success("Projeto '{$name}' criado com sucesso!");
        $io->listing([
            "cd {$name}",
            'php spark serve',
        ]);

        return Command::SUCCESS;
    }

    private function removeDir(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
        }

        rmdir($path);
    }

    private function commandExists(string $command): bool
    {
        $check = PHP_OS_FAMILY === 'Windows' ? "where {$command}" : "command -v {$command}";
        exec($check, $output, $code);
        return $code === 0;
    }
}
