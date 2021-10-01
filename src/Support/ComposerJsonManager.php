<?php

namespace AntonioPrimera\LaraPackager\Support;

class ComposerJsonManager
{
	/**
	 * Read the composer.json file and return
	 * the contents as an array
	 *
	 * @param $rootPath
	 *
	 * @return array|mixed
	 */
	public static function readFile(string $rootPath)
	{
		$filePath = Paths::path($rootPath, 'composer.json');
		
		if (!file_exists($filePath))
			return [];
		
		$fileContents = file_get_contents($filePath);
		return json_decode($fileContents, true);
	}
	
	/**
	 * Write the contents of a given array to composer.json
	 * Back up the original composer.json file
	 *
	 * @param string $rootPath
	 * @param array  $contents
	 */
	public static function writeFile(string $rootPath, array $contents)
	{
		$filePath = Paths::path($rootPath, 'composer.json');
		
		//if we already have a composer.json file, back it up as 'composer.json.bak'
		if (file_exists($filePath))
			copy($filePath, $filePath . '.bak');
		
		file_put_contents($filePath, json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}
	
	public static function updateArray(array $contents, array $updateData)
	{
		$updatedContents = $contents;
		
		//each item should have: ['action' => 'set|push', 'path' => 'dotSeparatedPath', 'value' => '*']
		foreach ($updateData as $item) {
			$action = $item['action'] ?? null;
			if ($action === 'set')
				Arrays::set($updatedContents, $item['path'], $item['value']);
			
			if ($action === 'push')
				Arrays::push($updatedContents, $item['path'], $item['value']);
		}
		
		return $updatedContents;
	}
}