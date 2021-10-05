<?php

namespace AntonioPrimera\LaraPackager\Commands;

use AntonioPrimera\LaraPackager\Actions\MergeGitIgnore;
use AntonioPrimera\LaraPackager\Support\Paths;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateGitignore extends Command
{
	protected static $defaultName = 'create:gitignore';
	
	protected static $defaultDescription = 'Creates a new .gitignore file in the package root, or merges the stub'
		. ' with the existing .gitignore file.';
	
	protected function configure()
	{
		//$this->addOption('debug', 'd', InputOption::VALUE_NONE, 'Enable debug mode');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln(
			file_exists(Paths::packageRootPath('.gitignore'))
				? "A .gitignore file already exists. Merging the packager .gitignore with the existing .gitignore file."
				: "No .gitignore file exists. Creating a new .gitignore file from the packager stub."
		);
		MergeGitIgnore::run();
		
		return Command::SUCCESS;
	}
}