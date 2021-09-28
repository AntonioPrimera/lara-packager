<?php

namespace Unit;

use AntonioPrimera\LaraPackager\Support\Arrays;
use AntonioPrimera\LaraPackager\Support\Namespaces;
use AntonioPrimera\LaraPackager\Support\Paths;
use AntonioPrimera\LaraPackager\Support\ServiceProviderName;
use PHPUnit\Framework\TestCase;

class SupportClassesTest extends TestCase
{
	/** @test */
	public function path_function_should_work_correctly()
	{
		$this->assertEquals('/src/test/level1/level2', Paths::path('/src/test', 'level1', 'level2'));
		$this->assertEquals('/src/test/level1/level2', Paths::path('/src/test/', '/level1/', '/level2/'));
		$this->assertEquals('src/test/level1/level2', Paths::path('src/test', '/level1', 'level2'));
	}
	
	/** @test */
	public function root_path_should_work_correctly()
	{
		$this->assertEquals(getcwd(), Paths::rootPath());
		$this->assertEquals('/src/test', Paths::rootPath('/src/test'));
	}
	
	/** @test */
	public function basic_namespace_creation_should_run_correctly()
	{
		$this->assertEquals(
			'AntonioPrimera\\LaraPackager\\Paths',
			Namespaces::create('\\AntonioPrimera\\LaraPackager', 'Paths')
		);
		
		$this->assertEquals(
			'AntonioPrimera\\LaraPackager\\Paths',
			Namespaces::create('\\AntonioPrimera\\LaraPackager\\', '\\Paths\\')
		);
		
		$this->assertEquals(
			'AntonioPrimera\\LaraPackager\\Paths',
			Namespaces::create('\\AntonioPrimera\\\\LaraPackager\\\\', '\\Paths\\\\')
		);
		
		$this->assertEquals(
			'AntonioPrimera\\LaraPackager\\Paths',
			Namespaces::create('\\AntonioPrimera\\\\', '\\\\LaraPackager\\', 'Paths\\')
		);
	}
	
	/** @test */
	public function composer_json_namespace_creation_should_run_correctly()
	{
		$this->assertEquals(
			'AntonioPrimera\\\\LaraPackager\\\\Paths\\\\',
			Namespaces::createForComposerAutoload('\\AntonioPrimera\\LaraPackager\\', '\\Paths\\')
		);
		
		$this->assertEquals(
			'AntonioPrimera\\\\LaraPackager\\\\Paths\\\\',
			Namespaces::createForComposerAutoload('\\\\AntonioPrimera\\LaraPackager\\', 'Paths')
		);
		
		$this->assertEquals(
			'AntonioPrimera\\\\LaraPackager\\\\Paths\\\\',
			Namespaces::createForComposerAutoload('AntonioPrimera', 'LaraPackager', 'Paths')
		);
	}
	
	/** @test */
	public function service_provider_name_should_be_generated_correctly()
	{
		$this->assertEquals(
			'AntonioPrimera\\\\LaraPackager\\\\LaraPackagerServiceProvider',
			ServiceProviderName::generate(
				'AntonioPrimera\\LaraPackager',
				'antonioprimera/lara-packager'
			)
		);
	}
}