<?php

namespace AntonioPrimera\LaraPackager\Actions;

use AntonioPrimera\LaraPackager\Support\Paths;

class MergeGitIgnore
{
	
	public static function run()
	{
		//get the existing and the stub git ignore file contents
		$stubFilePath = Paths::stubPath('.gitignore');
		$projectFilePath = Paths::packageRootPath('.gitignore');
		
		$stubContents = file_exists($stubFilePath) ? file_get_contents($stubFilePath) : null;
		$existingFileContents = file_exists($projectFilePath) ? file_get_contents($projectFilePath) : null;
		
		//split the files by lines
		$stubItems = $stubContents ? explode("\n", $stubContents) : [];
		$existingItems = $existingFileContents ? explode("\n", $existingFileContents) : [];
		
		//merge and remove duplicates
		$finalItemList = array_unique(array_filter(array_merge($stubItems, $existingItems)));
		
		//overwrite the existing file in the project folder
		file_put_contents(Paths::packageRootPath('.gitignore'), implode("\n", $finalItemList));
	}
}