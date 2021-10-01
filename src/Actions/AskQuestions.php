<?php

namespace AntonioPrimera\LaraPackager\Actions;

use AntonioPrimera\LaraPackager\Components\LaravelPackageComposerJson;
use AntonioPrimera\LaraPackager\Components\QuestionSet;
use AntonioPrimera\LaraPackager\Support\ServiceProviderName;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AskQuestions
{
	
	public static function run(
		Command $command,
		InputInterface $input,
		OutputInterface $output,
		LaravelPackageComposerJson $composerJson,
		bool $debugMode = false
	) : QuestionSet
	{
		$questions = static::setupQuestions($command, $input, $output, $composerJson, $debugMode);
		$questions->askAll();
		
		return $questions;
	}
	
	protected static function setupQuestions(
		Command $command,
		InputInterface $input,
		OutputInterface $output,
		LaravelPackageComposerJson $composerJson,
		bool $debugMode = false
	)
	{
		$questionSet = new QuestionSet();
		$questionSet->setupQuestionFactory($command, $input, $output)
			->setDebugMode($debugMode);
		
		$questionSet->createQuestion('packageName')
			->text('Package name')
			->defaultValue($composerJson->get('name', null))
			->example('namespace/package-name');
		
		$questionSet->createQuestion('packageDescription')
			->text('Package description')
			->defaultValue($composerJson->get('description', null));
		
		$questionSet->createQuestion('authorName')
			->text('Author name')
			->defaultValue($composerJson->get(['authors', 0, 'name'], null));
		
		$questionSet->createQuestion('authorEmail')
			->text('Author email')
			->defaultValue($composerJson->get(['authors', 0, 'email'], null));
		
		$questionSet->createQuestion('license')
			->text('License')
			->defaultValue($composerJson->get('license', 'MIT'));
		
		$questionSet->createQuestion('orchestraTestbench')
			->text('Do you want to use orchestra/testbench?')
			->yesNoQuestion()
			->defaultValue('y')
			->context($composerJson)
			->condition(function(LaravelPackageComposerJson $composerJson) {
				//if we already have it in the require-dev, don't ask this question
				return !$composerJson->requiresDev('orchestra/testbench');
			});
		
		$questionSet->createQuestion('orchestraTestbenchVersion')
			->text('Which version of orchestra/testbench do you want to use?')
			->defaultValue('^6.0')
			->condition(function($context, QuestionSet $questionSet) {
				return $questionSet->orchestraTestbench->answeredYes;
			});
		
		$questionSet->createQuestion('spatieLaravelPackageTools')
			->text('Do you want to use spatie/laravel-package-tools?')
			->yesNoQuestion()
			->defaultValue('y')
			->context($composerJson)
			->condition(function(LaravelPackageComposerJson $composerJson) {
				//if we already have it in the composer->require list, don't ask this question
				return !$composerJson->requires('spatie/laravel-package-tools');
			});
		
		$questionSet->createQuestion('spatieLaravelPackageToolsVersion')
			->text('Which version of spatie/laravel-package-tools do you want to use?')
			->defaultValue('^1.9')
			->condition(function($context, QuestionSet $questionSet) {
				return $questionSet->spatieLaravelPackageTools->answeredYes;
			});
		
		$questionSet->createQuestion('rootNamespace')
			->text('Root namespace')
			->example('AntonioPrimera\\LaraPackager');
		
		$questionSet->createQuestion('serviceProviderName')
			->text('Service Provider name')
			->defaultValue(
				function($context, QuestionSet $questionSet) {
					return ServiceProviderName::generateFromPackageName($questionSet->packageName->answer);
				},
			);
		
		return $questionSet;
	}
}