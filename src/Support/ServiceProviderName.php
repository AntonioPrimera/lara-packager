<?php

namespace AntonioPrimera\LaraPackager\Support;

class ServiceProviderName
{
	
	public static function nameWithNamespace(string $rootNamespace, string $serviceProviderName)
	{
		return Namespaces::create($rootNamespace, $serviceProviderName);
	}
	
	public static function generateFromPackageName(string $packageName)
	{
		$packageNameParts = explode('/', $packageName);
		$name = end($packageNameParts);
		
		$nameParts = array_map(function($part) {return ucfirst($part);}, explode('-', $name));
		$pascalCaseName = implode('', $nameParts);
		
		return $pascalCaseName . 'ServiceProvider';
	}
	
	public static function fileName(string $serviceProviderName)
	{
		return $serviceProviderName . '.php';
	}
}