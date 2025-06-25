<?php

namespace App\Foundation\Maker;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('create:entity')]
class MakeEntityCommand extends AbstractMakerCommand
{
    protected function configure(): void
    {
        $this
            ->setDescription('Create a new entity in the selected domain')
            ->addArgument('entityName', InputArgument::OPTIONAL, 'Entity name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $domain = $this->askDomain($io);

        /** @var string $entity */
        $entity = $input->getArgument('entityName');

        /** @var Application $application */
        $application = $this->getApplication();
        $command = $application->find('make:entity');
        $arguments = [
            'command' => 'make:entity',
            'name' => "\\App\\Domain\\$domain\\Entity\\$entity",
        ];

        $greatInput = new ArrayInput($arguments);

        return $command->run($greatInput, $output);
    }
}
