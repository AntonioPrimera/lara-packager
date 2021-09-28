<?php

namespace AntonioPrimera\LaraPackager\stubs;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
	public function boot()
	{
		//register necessary commands
		if ($this->app->runningInConsole()) {
			$this->commands([
				//MakeBapi::class,
			]);
		}
	}
}