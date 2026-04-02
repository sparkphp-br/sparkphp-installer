<?php

namespace SparkPhp\Installer\Commands;

use SparkPhp\Installer\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SelfUpdateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('self-update')
            ->setAliases(['selfupdate'])
            ->setDescription('Atualiza o SparkPHP Installer para a versão mais recente');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('SparkPHP Installer — Self Update');
        $io->text('Versão atual: <info>' . Application::VERSION . '</info>');
        $io->newLine();

        if (!$this->commandExists('composer')) {
            $io->error('Composer não encontrado. Instale em https://getcomposer.org');
            return Command::FAILURE;
        }

        $io->text('Verificando atualizações...');
        $io->newLine();

        passthru('composer global update sparkphp-br/installer', $result);

        if ($result !== 0) {
            $io->error('Falha ao atualizar o installer.');
            return Command::FAILURE;
        }

        $io->newLine();
        $io->success('Installer atualizado com sucesso!');

        return Command::SUCCESS;
    }

    private function commandExists(string $command): bool
    {
        $check = PHP_OS_FAMILY === 'Windows' ? "where {$command}" : "command -v {$command}";
        exec($check, $output, $code);
        return $code === 0;
    }
}
