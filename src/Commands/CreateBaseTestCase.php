<?php

namespace AntonioPrimera\LaraPackager\Commands;

use AntonioPrimera\LaraPackager\Components\TestEnvironment;
use AntonioPrimera\LaraPackager\Exceptions\InvalidNamespaceException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateBaseTestCase extends Command
{
	protected static $defaultName = 'create:base-test-case';
	
	protected static $defaultDescription = 'Creates the base TestCase class, inherited in all Tests of this package';
	
	protected function configure()
	{
		$this->setHelp(
			'Creates the base TestCase class, inherited in all Tests of this package. '
			. 'If this already exists, nothing is created'
		);
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$result = TestEnvironment::createPackageTestCase();
			
			$output->writeln(
				$result
					? 'The base TestCase class was created in /tests/TestCase.php'
					: 'The base TestCase already exists. No change was done.'
				);
		} catch (InvalidNamespaceException $exception) {
			$output->writeln('Test namespace could not be determined. Ensure that the test namespace is defined in your composer.json: autoload-dev.psr-4');
			return Command::FAILURE;
		}
		
		return Command::SUCCESS;
	}
}