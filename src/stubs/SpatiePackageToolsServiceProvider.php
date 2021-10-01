<?php

namespace DummyNamespace;

use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;

class DummyClass extends PackageServiceProvider
{
	
	public function configurePackage(Package $package): void
	{
		$package
			->name('DummyPackageName');
			//->hasConfigFile()
			//->hasViews()
			//->hasViewComponent('spatie', Alert::class)
			//->hasViewComposer('*', MyViewComposer::class)
			//->sharesDataWithAllViews('downloads', 3)
			//->hasTranslations()
			//->hasAssets()
			//->hasRoute('web')
			//->hasMigration('create_package_tables')
			//->hasCommand(YourCoolPackageCommand::class);
	}
}