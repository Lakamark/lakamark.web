<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'create:domain',
    description: 'To create a new domain structure',
)]
class CreateDomainCommand extends Command
{
    private array $folders = [
        'Event',
        'Repository',
        'Listener',
        'Subscriber'
    ];

    public function __construct(protected string $projectDir)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('domainName', InputArgument::OPTIONAL, 'domaine name')
            ->addOption('full', '-f', InputOption::VALUE_OPTIONAL, 'full domain structure', false)
            ->setHelp('Add -f option to create all children folders of your domain');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $domainName = $input->getArgument('domainName');

        if (!$domainName) {
            $io->error('You should specify a domain name');
            return Command::FAILURE;
        }
        $option = $input->getOption('full');
        if ($option !== false) {
            // Create all folders
        } else {
        }



        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
