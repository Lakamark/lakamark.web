<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'create:domain',
    description: 'To create a new domain structure',
)]
class CreateDomainCommand extends Command
{
    /**
     * Folders to create in the new domain
     * @var array|string[]
     */
    private array $folders = [
        'Entity',
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
        $directory = "$this->projectDir/src/Domain/$domainName";
        $fileSystem = new Filesystem();

        // If the user forgets to defin the domain name return an error.
        if (!$domainName) {
            $io->error('You should specify a domain name');
            return Command::FAILURE;
        }

        // If the user wants a simple empty domain
        // Or the user wants all necessary folders in his domain
        $option = $input->getOption('full');
        if ($option !== false) {
            // Create all folders (Entity, Repository etc.)
            foreach ($this->folders as $folder) {
                $fileSystem->mkdir($directory . '/' . $folder);
            }
        } else {
            // Create a simple folder in the domain folder
            $fileSystem->mkdir($directory);
        }

        $io->success("The domain $domainName was created. Now you can run the command create:entity command");

        return Command::SUCCESS;
    }
}
