<?php

namespace AntonioPrimera\LaraPackager\Commands;

use AntonioPrimera\LaraPackager\Components\TestEnvironment;
use AntonioPrimera\LaraPackager\Exceptions\InvalidNamespaceException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeTest extends Command
{
	protected static $defaultName = 'make:test';
	
	protected static $defaultDescription = 'Creates a new feature / unit test file. Specify --unit (-u) in order'
		. ' to create a unit test file.';
	
	protected function configure()
	{
		$this->setHelp('Create a new Test Class. Use --unit or --feature to determine where to place the file.')
			->addArgument('name', InputArgument::REQUIRED, 'The name of the testCase (e.g. UpdateStoreTest)')
			->addOption('unit', 'u', InputOption::VALUE_NONE, 'Create a unit test');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$result = TestEnvironment::makeTest($input->getArgument('name'), $input->getOption('unit'));
			
			$output->writeln(
				"The new "
				. ($result['unit'] ? 'unit' : 'feature')
				. " test {$result['className']} was created at {$result['filePath']}");
		} catch (InvalidNamespaceException $exception) {
			$output->writeln('Test namespace could not be determined. Ensure that the test namespace is defined in your composer.json: autoload-dev.psr-4');
			return Command::FAILURE;
		}
		
		return Command::SUCCESS;
	}
}