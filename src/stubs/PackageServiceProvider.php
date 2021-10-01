<?php

namespace DummyNamespace;
use Illuminate\Support\ServiceProvider;

class DummyClass extends ServiceProvider
{
	public function boot()
	{
		//if ($this->app->runningInConsole()) {
		//	$this->commands([
		//		MyCommand::class,
		//	]);
		//}
	}
}