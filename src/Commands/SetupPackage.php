<?php

namespace AntonioPrimera\LaraPackager\Commands;

use AntonioPrimera\LaraPackager\Actions\CreateFolderStructure;
use AntonioPrimera\LaraPackager\Actions\UpdateComposerJson;
use AntonioPrimera\LaraPackager\Support\Paths;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupPackage extends Command
{
	protected static $defaultName = 'setup:package';
	
	protected static $defaultDescription = 'Setup the file structure for the package';
	
	protected function configure()
	{
		//$this->addArgument('packageName', InputArgument::REQUIRED, 'Package name');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$verbose = true;
		
		$output->writeln("Updating the composer.json");
		UpdateComposerJson::run(Paths::rootPath(), $this, $input, $output, $verbose);
		
		$output->writeln("Creating the package folders");
		CreateFolderStructure::run(getcwd(), $output, $verbose);
		
		$output->writeln("Creating the ServiceProvider");
		
		//$this->createFolders($output);
		
		//$helper = $this->getHelper('question');
		//
		//$question = new Question('Package name: ');
		//$packageName = $helper->ask($input, $output, $question);
		//
		//$output->writeln("Your package name is: " . $packageName);
		
		return Command::SUCCESS;
	}
}