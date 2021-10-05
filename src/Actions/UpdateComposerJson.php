<?php

namespace AntonioPrimera\LaraPackager\Actions;

use AntonioPrimera\LaraPackager\Components\LaravelPackageComposerJson;
use AntonioPrimera\LaraPackager\Support\Namespaces;
use AntonioPrimera\LaraPackager\Components\QuestionSet;
use AntonioPrimera\LaraPackager\Support\ServiceProviderName;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateComposerJson
{
	
	public static function run(
		LaravelPackageComposerJson $composerJson,
		QuestionSet $questionSet,
		Command $command,
		InputInterface $input,
		OutputInterface $output,
		$verbose
	)
	{
		static::updateComposerJson($composerJson, $questionSet);
		$composerJson->writeFile(true);
	}
	
	protected static function updateComposerJson(LaravelPackageComposerJson $composerJson, QuestionSet $questions)
	{
		$rootNamespace = Namespaces::createForComposerAutoload($questions->rootNamespace->answer);
		$testNamespace = Namespaces::createForComposerAutoload($rootNamespace, 'Tests');
		
		$composerJson->setName($questions->packageName->answer)
			->setDescription($questions->packageDescription->answer)
			->setLicense($questions->license->answer)
			->setAuthor($questions->authorName->answer, $questions->authorEmail->answer)
			->addPsr4Autoload($rootNamespace, 'src/', false)
			->addPsr4Autoload($testNamespace, 'tests/', true);
		
		if ($questions->orchestraTestbench->answeredYes)
			$composerJson->addRequired(
				'orchestra/testbench',
				$questions->orchestraTestbenchVersion->answer,
				true
			);
		
		if ($questions->spatieLaravelPackageTools->answeredYes)
			$composerJson->addRequired(
				'spatie/laravel-package-tools',
				$questions->spatieLaravelPackageToolsVersion->answer,
				false
			);
		
		if ($questions->createServiceProvider->answeredYes)
			$composerJson->addServiceProvider(
				ServiceProviderName::nameWithNamespace(
					$rootNamespace,
					$questions->serviceProviderName->answer
				)
			);
	}
}