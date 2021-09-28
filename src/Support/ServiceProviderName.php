<?php

namespace AntonioPrimera\LaraPackager\Support;

class ServiceProviderName
{
	
	public static function generate(string $rootNamespace, string $packageName)
	{
		$packageNameParts = explode('/', $packageName);
		$name = end($packageNameParts);
		
		$nameParts = array_map(function($part) {return ucfirst($part);}, explode('-', $name));
		$pascalCaseName = implode('', $nameParts);
		
		return Namespaces::doubleBackSlashes(
			Namespaces::create($rootNamespace, $pascalCaseName . 'ServiceProvider')
		);
	}
}