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
	
	//protected static function setupQuestions(
	//	Command $command,
	//	InputInterface $input,
	//	OutputInterface $output,
	//	LaravelPackageComposerJson $composerJson
	//)
	//{
	//	$questionSet = new QuestionSet();
	//	$questionSet->setupQuestionFactory($command, $input, $output);
	//
	//	$questionSet->createQuestion('packageName')
	//		->text('Package name')
	//		->defaultValue($composerJson->get('name', null))
	//		->example('namespace/package-name');
	//
	//	$questionSet->createQuestion('packageDescription')
	//		->text('Package description')
	//		->defaultValue($composerJson->get('description', null));
	//
	//	$questionSet->createQuestion('authorName')
	//		->text('Author name')
	//		->defaultValue($composerJson->get(['authors', 0, 'name'], null));
	//
	//	$questionSet->createQuestion('authorEmail')
	//		->text('Author email')
	//		->defaultValue($composerJson->get(['authors', 0, 'email'], null));
	//
	//	$questionSet->createQuestion('license')
	//		->text('License')
	//		->defaultValue($composerJson->get('license', 'MIT'));
	//
	//	$questionSet->createQuestion('orchestraTestbench')
	//		->text('Do you want to use orchestra/testbench?')
	//		->yesNoQuestion()
	//		->defaultValue('y')
	//		->context($composerJson)
	//		->condition(function(LaravelPackageComposerJson $composerJson) {
	//			//if we already have it in the require-dev, don't ask this question
	//			return !$composerJson->requiresDev('orchestra/testbench');
	//		});
	//
	//	$questionSet->createQuestion('orchestraTestbenchVersion')
	//		->text('Which version of orchestra/testbench do you want to use?')
	//		->defaultValue('^6.0')
	//		->condition(function($context, QuestionSet $questionSet) {
	//			return $questionSet->orchestraTestbench->answeredYes;
	//		});
	//
	//	$questionSet->createQuestion('spatieLaravelPackageTools')
	//		->text('Do you want to use spatie/laravel-package-tools?')
	//		->yesNoQuestion()
	//		->defaultValue('y')
	//		->context($composerJson)
	//		->condition(function(LaravelPackageComposerJson $composerJson) {
	//			//if we already have it in the composer->require list, don't ask this question
	//			return !$composerJson->requires('spatie/laravel-package-tools');
	//		});
	//
	//	$questionSet->createQuestion('orchestraTestbenchVersion')
	//		->text('Which version of spatie/laravel-package-tools do you want to use?')
	//		->defaultValue('^1.9')
	//		->condition(function($context, QuestionSet $questionSet) {
	//			return $questionSet->spatieLaravelPackageTools->answeredYes;
	//		});
	//
	//	$questionSet->createQuestion('rootNamespace')
	//		->text('Root namespace')
	//		->example('AntonioPrimera\\LaraPackager');
	//
	//	$questionSet->createQuestion('serviceProviderName')
	//		->text('Service Provider name')
	//		->defaultValue(
	//			function($context, QuestionSet $questionSet) {
	//				return ServiceProviderName::generateFromPackageName($questionSet->packageName->answer);
	//			},
	//		);
	//
	//	return $questionSet;
	//}
	
	protected static function updateComposerJson(LaravelPackageComposerJson $composerJson, QuestionSet $questions)
	{
		$rootNamespace = Namespaces::createForComposerAutoload($questions->rootNamespace->answer);
		$testNamespace = Namespaces::createForComposerAutoload($rootNamespace, 'Tests');
		
		$composerJson->setName($questions->packageName->answer)
			->setDescription($questions->packageDescription->answer)
			->setLicense($questions->license->answer)
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
		
		$composerJson->addServiceProvider(
			ServiceProviderName::nameWithNamespace(
				$rootNamespace,
				$questions->serviceProviderName->answer
			)
		);
	}
	
	//protected static function prepareUpdateData() : array
	//{
	//	$qs = static::$questionSet;
	//
	//	$rootNamespace = Namespaces::createForComposerAutoload($qs->rootNamespace->answer);
	//	$testNamespace = Namespaces::createForComposerAutoload($rootNamespace, 'Tests');
	//
	//	return [
	//		//'packageName' => [
	//		//	'path'		=> 'name',
	//		//	'action'  	=> 'set',
	//		//	'value'	 	=> strtolower($q['packageName']['answer']),
	//		//],
	//		//
	//		//'packageDescription' => [
	//		//	'path'		=> 'description',
	//		//	'action'  	=> 'set',
	//		//	'value'	 	=> $q['packageDescription']['answer'],
	//		//],
	//		//
	//		//'license' => [
	//		//	'path'	   => 'license',
	//		//	'action'   => 'set',
	//		//	'value'	 	=> $q['license']['answer'],
	//		//],
	//
	//		//'orchestraTestbench' => [
	//		//	'path'		=> 'require-dev.orchestra/testbench',
	//		//	'action'	=> 'set',
	//		//	'value'	 	=> stripos($q['orchestraTestbench']['answer'], 'y') !== false
	//		//		? $q['orchestraTestbenchVersion']['answer']
	//		//		: false,
	//		//],
	//		//
	//		//'spatiePackageTools' => [
	//		//	'path'		=> 'require.spatie/laravel-package-tools',
	//		//	'action'	=> 'set',
	//		//	'value'	 	=> stripos($q['spatieLaravelPackageTools']['answer'], 'y') !== false
	//		//		? $q['spatieLaravelPackageToolsVersion']['answer']
	//		//		: false,
	//		//],
	//
	//		//'autoload' => [
	//		//	'path'		=> ['autoload', 'psr-4', $rootNamespace],
	//		//	'action'	=> 'set',
	//		//	'value'		=> 'src/',
	//		//],
	//		//
	//		//'autoloadDev' => [
	//		//	'path'		=> ['autoload-dev', 'psr-4', $testNamespace],
	//		//	'action'	=> 'set',
	//		//	'value'		=> 'tests/',
	//		//],
	//
	//		//'serviceProvider' => [
	//		//	'path'		=> 'extra.laravel.providers',
	//		//	'action'	=> 'push',
	//		//	'value'		=> ServiceProviderName::nameWithNamespace(
	//		//		$rootNamespace,
	//		//		$q['serviceProviderName']['answer']
	//		//	),
	//		//],
	//	];
	//}
}

//{
//	"name": "antonioprimera/bapi",
//    "description": "The business layer base for a Laravel Application",
//    "type": "library",
//    "license": "mit",
//    "authors": [
//        {
//			"name": "Antonio Primera",
//            "email": "antonio@cus.ro"
//        }
//    ],
//    "minimum-stability": "dev",
//    "require": {
//	"illuminate/support": "^8",
//        "illuminate/console": "^8"
//    },
//    "require-dev": {
//	"orchestra/testbench": "6.x-dev"
//    },
//
//    "extra": {
//	"laravel": {
//		"providers": [
//			"AntonioPrimera\\Bapi\\Providers\\BapiPackageServiceProvider"
//		]
//        }
//    },
//
//    "autoload": {
//	"psr-4": {
//		"AntonioPrimera\\Bapi\\": "src/"
//        }
//    },
//
//    "autoload-dev": {
//	"psr-4": {
//		"AntonioPrimera\\Bapi\\Tests\\": "tests/"
//        }
//    }
//}
