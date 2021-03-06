<?php

namespace AntonioPrimera\LaraPackager\Actions;

use AntonioPrimera\LaraPackager\Components\FileManager;
use AntonioPrimera\LaraPackager\Components\QuestionSet;
use AntonioPrimera\LaraPackager\Support\Namespaces;
use AntonioPrimera\LaraPackager\Support\Paths;
use AntonioPrimera\LaraPackager\Support\ServiceProviderName;

class CreateServiceProvider
{
	public static function run(QuestionSet $questions)
	{
		if (!$questions->createServiceProvider->answeredYes)
			return;
		
		$stubName = $questions->spatieLaravelPackageTools->answeredYes
			? 'SpatiePackageToolsServiceProvider.php'
			: 'PackageServiceProvider.php';
		
		FileManager::createFromStub(
			Paths::stubPath($stubName),
			Paths::path(Paths::packageRootPath('src'), ServiceProviderName::fileName($questions->serviceProviderName->answer)),
			[
				'DummyNamespace'   => Namespaces::create($questions->rootNamespace->answer),
				'DummyClass'	   => $questions->serviceProviderName->answer,
				'DummyPackageName' => $questions->packageName->answer,
			]
		);
	}
	
	//protected static function createFromStub(string $stubPath, string $destinationPath, array $replace = [])
	//{
	//	$contents = file_get_contents($stubPath);
	//	foreach ($replace as $key => $value)
	//		$contents = str_replace($key, $value, $contents);
	//
	//	file_put_contents($destinationPath, $contents);
	//}
}