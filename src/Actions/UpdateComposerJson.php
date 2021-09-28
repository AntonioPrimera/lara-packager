<?php

namespace AntonioPrimera\LaraPackager\Actions;

use AntonioPrimera\LaraPackager\Support\Arrays;
use AntonioPrimera\LaraPackager\Support\ComposerJsonManager;
use AntonioPrimera\LaraPackager\Support\Namespaces;
use AntonioPrimera\LaraPackager\Support\Paths;
use AntonioPrimera\LaraPackager\Support\ServiceProviderName;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UpdateComposerJson
{
	
	protected static $questions;
	
	public static function run(
		string $rootPath,
		Command $command,
		InputInterface $input,
		OutputInterface $output,
		$verbose
	)
	{
		$composerJson = ComposerJsonManager::readFile($rootPath);
		static::setupQuestions($composerJson);
		static::askQuestions($command, $input, $output);
		
		$updateData = static::prepareUpdateData();
		$updatedComposerJson = ComposerJsonManager::updateArray($composerJson, $updateData);
		
		ComposerJsonManager::writeFile($rootPath, $updatedComposerJson);
	}
	
	protected static function prepareUpdateData() : array
	{
		$rootNamespace = Namespaces::createForComposerAutoload(static::$questions['rootNamespace']['answer']);
		$testNamespace = Namespaces::createForComposerAutoload(
			static::$questions['rootNamespace']['answer'],
			'Tests'
		);
		
		return [
			'packageName' => [
				'path'		=> 'name',
				'action'  	=> 'set',
				'value'	 	=> static::$questions['packageName']['answer'],
			],
			
			'packageDescription' => [
				'path'		=> 'description',
				'action'  	=> 'set',
				'value'	 	=> static::$questions['packageDescription']['answer'],
			],
			
			'license' => [
				'path'	   => 'license',
				'action'   => 'set',
				'value'	 	=> static::$questions['license']['answer'],
			],
			
			'orchestraTestbench' => [
				'path'		=> 'require-dev.orchestra/testbench',
				'action'	=> 'set',
				'value'	 	=> static::$questions['orchestraTestbenchVersion']['answer'],
			],
			
			'autoload' => [
				'path'		=> ['autoload', 'psr-4', $rootNamespace],
				'action'	=> 'set',
				'value'		=> 'src/',
			],
			
			'autoloadDev' => [
				'path'		=> ['autoload-dev', 'psr-4', $testNamespace],
				'action'	=> 'set',
				'value'		=> 'tests/',
			],
			
			'serviceProvider' => [
				'path'		=> 'extra.laravel.providers',
				'action'	=> 'push',
				'value'		=> ServiceProviderName::generate(
					$rootNamespace,
					static::$questions['packageName']['answer']
				),
			],
		];
	}
	
	protected static function setupQuestions(array $composerJson)
	{
		static::$questions = [
			'packageName' => [
				'question' => 'Package name (namespace/package-name): ',
				'path'	   => 'name',
				'action'   => 'set',
				'default'  => $composerJson['name'] ?? null,
			],
			'packageDescription' => [
				'question' => 'Package description: ',
				'path'	   => 'description',
				'action'   => 'set',
				'default'  => $composerJson['description'] ?? null,
			],
			
			'authorName' => [
				'question' => 'Author name: ',
				'path'	   => ['authors', 0, 'name'],
				'action'   => 'set',
				'default'  => $composerJson['authors'][0]['name'] ?? null,
			],
			'authorEmail' => [
				'question' => 'Author email: ',
				'path'	   => ['authors', 0, 'email'],
				'action'   => 'set',
				'default'  => $composerJson['authors'][0]['email'] ?? null,
			],
			'license' => [
				'question' => 'License: ',
				'path'	   => 'license',
				'action'   => 'set',
				'default'  => $composerJson['license'] ?? null,
			],
			
			'orchestraTestbench' => [
				'question'  => 'Do you want to use Orchestra/Testbench [y/n] ',
				'action'	=> false,
				'condition' => function() use ($composerJson) {
					//if we already have it in the require-dev, don't ask this question
					$requireDev = $composerJson['require-dev'] ?? [];
					foreach ($requireDev as $item)
						if (stripos($item, 'orchestra/testbench') !== false)
							return false;
					
					return true;
				}
			],
			'orchestraTestbenchVersion' => [
				'question'  => 'Which version of orchestra/testbench do you want to use? (e.g. ^6.0) ',
				'path'		=> 'require-dev.orchestra/testbench',
				'action'	=> 'set',
				'condition' => function() {
					return static::$questions['orchestraTestbench']['answer'] ?? false;
				}
			],
			
			'rootNamespace' => [
				'question' => 'Root namespace (e.g. AntonioPrimera\\LaraPackager) : ',
			],
		];
	}
	
	protected static function askQuestions(
		Command $command,
		InputInterface $input,
		OutputInterface $output
	)
	{
		$helper = $command->getHelper('question');
		foreach (static::$questions as $questionData) {
			//if there is a callable condition, and it returns false, don't ask this question
			if (is_callable($questionData['condition'] ?? false) && !$questionData['condition']())
				continue;
			
			$question = new Question($questionData['question'], $questionData['default'] ?? false);
			$questionData['answer'] = $helper->ask($input, $output, $question);
		}
	}
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
