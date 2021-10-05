<?php

namespace AntonioPrimera\LaraPackager\Components;

use AntonioPrimera\LaraPackager\Exceptions\InvalidNamespaceException;
use AntonioPrimera\LaraPackager\Support\Paths;

class TestEnvironment
{
	
	public static function createFolders()
	{
		FolderStructure::create([
			'tests',
			'tests' . DIRECTORY_SEPARATOR . 'Unit',
			'tests' . DIRECTORY_SEPARATOR . 'Feature',
			'tests' . DIRECTORY_SEPARATOR . 'TestContext',
		]);
	}
	
	public static function createPackageTestCase()
	{
		$stubFilePath = Paths::stubPath('TestCase.php');
		$projectFilePath = Paths::packageRootPath('tests', 'TestCase.php');
		
		if (file_exists($projectFilePath))
			return false;
		
		FileManager::createFromStub(
			$stubFilePath,
			$projectFilePath,
			[
				'DummyNamespace'   		=> trim(static::getTestsNamespace(), '\\'),
				'DummyParentTestCase'	=> static::usesOrchestraTestbench()
					? '\\Orchestra\\Testbench\\TestCase'
					: '\\PHPUnit\\Framework\\TestCase',
			]
		);
		
		return true;
	}
	
	/**
	 * @throws InvalidNamespaceException
	 */
	public static function makeTest($name, $unit)
	{
		$testNamespace = static::getTestsNamespace();
		if (!$testNamespace)
			throw new InvalidNamespaceException();
		
		//make sure the className (and thus also the filename ends in "Test")
		$className = $name . (static::nameEndsIn($name, 'Test') ? '' : 'Test');
		$fileName = $className . '.php';
		
		$filePath = Paths::packageRootPath('tests', $unit ? 'Unit' : 'Feature', $fileName);
		
		FileManager::createFromStub(
			Paths::stubPath('DummyTest.php'),
			$filePath,
			[
				'DummyNamespace'	  => $testNamespace . ($unit ? 'Unit' : 'Feature'),
				'DummyClass' 		  => $className,
				'DummyParentTestCase' => static::packageTestCase($testNamespace),
				'DummyUnitOption'	  => $unit ? '-u' : '',
			]
		);
		
		return compact('className', 'filePath', 'unit');
	}
	
	public static function createPhpUnitXml()
	{
		FileManager::createFromStub(
			Paths::stubPath('phpunit.xml'),
			Paths::packageRootPath('phpunit.xml')
		);
	}
	
	//--- Wrapper methods ---------------------------------------------------------------------------------------------
	
	public static function setup()
	{
		static::createFolders();
		static::createPackageTestCase();
		static::createPhpUnitXml();
	}
	
	//--- Public helpers ----------------------------------------------------------------------------------------------
	
	public static function getTestsNamespace()
	{
		$composerJson = static::getComposerJson();
		$autoloadDevList = $composerJson->get('autoload-dev.psr-4', []);
		$autoloadList = $composerJson->get('autoload.psr-4', []);
		
		foreach (array_merge($autoloadDevList, $autoloadList) as $namespace => $path)
			if (trim($path, '/') === 'tests')
				return $namespace;
		
		return null;
	}
	
	public static function usesOrchestraTestbench()
	{
		$composerJson = static::getComposerJson();
		return array_key_exists('orchestra/testbench', $composerJson->get('require-dev', []))
			|| array_key_exists('orchestra/testbench', $composerJson->get('require', []));
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected static function getComposerJson()
	{
		return new ComposerJson(Paths::packageRootPath('composer.json'));
	}
	
	protected static function nameEndsIn($name, $ending)
	{
		//e.g. nameEndsIn('StoreTest', 'Test') = true
		return stripos($name, $ending) === strlen($name) - strlen($ending);
	}
	
	protected static function packageTestCase($testNamespace)
	{
		//ideally a base TestCase at project level
		if (file_exists(Paths::packageRootPath('tests', 'TestCase.php')))
			return $testNamespace . 'TestCase';
		
		//second, check if orchestra testbench is used, to inherit its testcase
		if (static::usesOrchestraTestbench())
			return '\\Orchestra\\Testbench\\TestCase';
		
		//worst case scenario, the basic phpunit testcase is used
		return '\\PHPUnit\\Framework\\TestCase';
	}
}