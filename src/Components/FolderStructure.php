<?php

namespace AntonioPrimera\LaraPackager\Components;

use AntonioPrimera\LaraPackager\Support\Paths;

class FolderStructure
{
	
	public static function create(string|iterable $folders, $rootPath = null)
	{
		$folderList = is_string($folders) ? [$folders] : $folders;
		$packageRootPath = $rootPath ?: Paths::packageRootPath();
		$result = [
			'created' 	=> [],
			'existing'	=> []
		];
		
		foreach ($folders as $folder) {
			$fullPath = Paths::path($packageRootPath, $folder);
			
			if (!is_dir($fullPath)) {
				mkdir($fullPath);
				$result['created'][] = compact('folder', 'fullPath');
			} else {
				$result['existing'][] = compact('folder', 'fullPath');
			}
		}
		
		return $result;
	}
}