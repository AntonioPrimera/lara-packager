<?php

namespace AntonioPrimera\LaraPackager\Commands;

use AntonioPrimera\LaraPackager\Actions\AskQuestions;
use AntonioPrimera\LaraPackager\Actions\CreateFolderStructure;
use AntonioPrimera\LaraPackager\Actions\CreateServiceProvider;
use AntonioPrimera\LaraPackager\Actions\MergeGitIgnore;
use AntonioPrimera\LaraPackager\Actions\UpdateComposerJson;
use AntonioPrimera\LaraPackager\Components\FileManager;
use AntonioPrimera\LaraPackager\Components\LaravelPackageComposerJson;
use AntonioPrimera\LaraPackager\Support\Paths;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetupPackage extends Command
{
	protected static $defaultName = 'setup:package';
	
	protected static $defaultDescription = 'Setup the file structure for the package';
	
	protected function configure()
	{
		$this->addOption('debug', 'd', InputOption::VALUE_NONE, 'Enable debug mode');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		//$output->writeln(Paths::packageRootPath());
		//$output->writeln(Paths::packageRootPath('gigi', 'migi'));
		//
		//$output->writeln(Paths::rootPath());
		//$output->writeln(Paths::rootPath('gigi', 'migi'));
		//
		//$output->writeln(Paths::stubPath('gigismigis.php'));
		//die();
		
		$verbose = true;
		$rootPath = Paths::packageRootPath();
		
		//read the existing composer json
		$composerJson = new LaravelPackageComposerJson($rootPath);
		
		$questions = AskQuestions::run($this, $input, $output, $composerJson, $input->getOption('debug'));
		
		$output->writeln("Updating the composer.json");
		UpdateComposerJson::run($composerJson, $questions, $this, $input, $output, $verbose);
		
		$output->writeln("Creating the package folders");
		CreateFolderStructure::run(getcwd(), $output, $verbose);
		
		$output->writeln("Creating the ServiceProvider");
		CreateServiceProvider::run($questions);
		
		$output->writeln("Creating the readme file");
		FileManager::createFromStub(
			Paths::stubPath('readme.md'),
			Paths::path(Paths::packageRootPath(), 'readme.md'),
		);
		
		$output->writeln("Merging the packager .gitignore with the existing .gitignore file");
		MergeGitIgnore::run();
		
		$output->writeln("Running composer update");
		exec('composer update');
		
		return Command::SUCCESS;
	}
}