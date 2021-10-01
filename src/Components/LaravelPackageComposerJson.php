<?php

namespace AntonioPrimera\LaraPackager\Components;

use AntonioPrimera\LaraPackager\Support\Arrays;

class LaravelPackageComposerJson extends ComposerJson
{
	
	public function addServiceProvider($name)
	{
		//"extra": {
		//	"laravel": {
		//		"providers": [
		//			"AntonioPrimera\\Bapi\\Providers\\BapiPackageServiceProvider"
		//		]
		//	}
		//},
		
		$providerList = Arrays::get($this->contents, 'extra.laravel.providers', []);
		if (!is_array($providerList))
			$providerList = [];
		
		//make sure it's not already in the list
		if (!in_array($name, $providerList))
			$providerList[] = $name;
		
		Arrays::set($this->contents, 'extra.laravel.providers', $providerList);
		
		return $this;
	}
}