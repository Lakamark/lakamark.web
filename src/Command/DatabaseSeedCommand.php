<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'database:seed',
    description: 'Seed the database with fake data',
)]
class DatabaseSeedCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Call the command hautelook:fixtures:load
        // I know it is just a shortcut to the hautelook command
        // I am a lazy guy ;)
        $command = $this->getApplication()->find('hautelook:fixtures:load');
        $result = $command->run($input, $output);

        if (Command::SUCCESS !== $result) {
            return $result;
        }

        $io->success('Your database seeders have been loaded.');

        return Command::SUCCESS;
    }
}
