<?php

namespace Feature;

use AntonioPrimera\LaraPackager\Actions\UpdateComposerJson;
use AntonioPrimera\LaraPackager\Support\ComposerJsonManager;

class UpdateComposerJsonTest extends \PHPUnit\Framework\TestCase
{
	protected $rootPath;
	
	protected function setUp(): void
	{
		parent::setUp();
		
		$this->rootPath = dirname(__DIR__) . '/TestContext/composerJsonFiles';
		
		//remove unwanted files
		$removeFiles = [
			$this->rootPath . '/sample1/composer.json.bak',
			$this->rootPath . '/sample2/composer.json.bak',
			$this->rootPath . '/sample1/composer.json',
			$this->rootPath . '/sample2/composer.json'
		];
		
		foreach ($removeFiles as $fileName) {
			if (file_exists($fileName))
				unlink($fileName);
		}
		
		//copy the composer files from the originals
		copy($this->rootPath . '/sample1/original.composer.json', $this->rootPath . '/sample1/composer.json');
		copy($this->rootPath . '/sample2/original.composer.json', $this->rootPath . '/sample2/composer.json');
		
		$this->assertFileExists($this->rootPath . '/sample1/original.composer.json');
		$this->assertFileExists($this->rootPath . '/sample2/original.composer.json');
		$this->assertFileExists($this->rootPath . '/sample1/composer.json');
		$this->assertFileExists($this->rootPath . '/sample2/composer.json');
		
		$this->assertFileDoesNotExist($this->rootPath . '/sample1/composer.json.bak');
		$this->assertFileDoesNotExist($this->rootPath . '/sample2/composer.json.bak');
	}
	
	/** @test */
	public function it_can_read_a_composer_json_file()
	{
		//--- Composer json contents ---------------
		//"name": "someVendorName/somePackageName",
		//"description": "some description",
		//"minimum-stability": "some stability",
		//"license": "some license",
		//"authors": [
		//	{
		//		"name": "dev",
		//		"email": "email@example.com"
		//	}
		//]
		
		$composerJsonContents = ComposerJsonManager::readFile($this->rootPath . '/sample1');
		
		$this->assertIsArray($composerJsonContents);
		$this->assertArrayHasKey('name', $composerJsonContents);
		$this->assertArrayHasKey('description', $composerJsonContents);
		
		$this->assertEquals('some license', $composerJsonContents['license'] ?? null);
		$this->assertEquals('email@example.com', $composerJsonContents['authors'][0]['email'] ?? null);
	}
	
	/** @test */
	public function it_can_write_a_composer_json_file()
	{
		$path = $this->rootPath . '/sample1';
		$composerJsonContents = ComposerJsonManager::readFile($path);
		
		$composerJsonContents['newKey'] = 'newValue';
		$composerJsonContents['authors'][] = [
			'name'  => 'eva',
			'email' => 'eva@mendez.com',
		];
		
		//remove the backup file if it exists (maybe from an unsuccessful previous test)
		$backupFile = $path . '/composer.json.bak';
		
		ComposerJsonManager::writeFile($path, $composerJsonContents);
		
		$this->assertFileExists($backupFile);
		$newComposerJsonContents = ComposerJsonManager::readFile($path);
		
		$this->assertArrayHasKey('newKey', $newComposerJsonContents);
		$this->assertEquals('newValue', $newComposerJsonContents['newKey']);
		
		$this->assertCount(2, $newComposerJsonContents['authors']);
		$this->assertEquals('eva', $newComposerJsonContents['authors'][1]['name']);
	}
	
	/** @test */
	public function it_can_correctly_update_a_json_file()
	{
		$path = $this->rootPath . '/sample1';
		$composerJsonContents = ComposerJsonManager::readFile($path);
		
		$updates = [
			'newKey1' => [
				'action' => 'set',
				'value'	 => 'newValue1',
				'path'	 => 'newSuperKey1.newKey1',
			],
			
			'newKey2' => [
				'action' => 'set',
				'value'  => 'newValue2',
				'path'	 => 'newKey2',
			],
			
			'newArrayValue' => [
				'action' => 'push',
				'path'   => 'authors',
				'value'  => [
					'name'  => 'gigi',
					'email' => 'gigi@hadid.com',
				]
			],
			
			'newKeyAndArrayValue' => [
				'action' => 'push',
				'path'   => 'newArray',
				'value'  => 'John Goodman',
			],
		];
		
		$update = ComposerJsonManager::updateArray($composerJsonContents, $updates);
		
		$this->assertArrayHasKey('newSuperKey1', $update);
		$this->assertIsArray($update['newSuperKey1']);
		$this->assertArrayHasKey('newKey1', $update['newSuperKey1']);
		$this->assertEquals('newValue1', $update['newSuperKey1']['newKey1']);
		
		$this->assertArrayHasKey('newKey2', $update);
		$this->assertEquals('newValue2', $update['newKey2']);
		
		$this->assertCount(2, $update['authors']);
		$this->assertEquals('gigi', $update['authors'][1]['name']);
		
		$this->assertArrayHasKey('newArray', $update);
		$this->assertIsArray($update['newArray']);
		$this->assertCount(1, $update['newArray']);
		$this->assertEquals('John Goodman', $update['newArray'][0]);
	}
}