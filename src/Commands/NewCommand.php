<?php

namespace SparkPhp\Installer\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NewCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('new')
            ->setDescription('Cria um novo projeto SparkPHP')
            ->addArgument('name', InputArgument::REQUIRED, 'Nome do projeto');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        $io->title('SparkPHP Installer');
        $io->text("Criando projeto <info>{$name}</info>...");
        $io->newLine();

        if (!$this->commandExists('composer')) {
            $io->error('Composer não encontrado. Instale em https://getcomposer.org');
            return Command::FAILURE;
        }

        if (is_dir($name)) {
            $io->error("O diretório '{$name}' já existe.");
            return Command::FAILURE;
        }

        $command = sprintf(
            'composer create-project sparkphp-br/sparkphp %s',
            escapeshellarg($name)
        );

        passthru($command, $result);

        if ($result !== 0) {
            $io->error('Falha ao criar o projeto.');
            return Command::FAILURE;
        }

        $io->newLine();
        $io->success("Projeto '{$name}' criado com sucesso!");
        $io->listing([
            "cd {$name}",
            'php spark serve',
        ]);

        return Command::SUCCESS;
    }

    private function commandExists(string $command): bool
    {
        $check = PHP_OS_FAMILY === 'Windows' ? "where {$command}" : "command -v {$command}";
        exec($check, $output, $code);
        return $code === 0;
    }
}
