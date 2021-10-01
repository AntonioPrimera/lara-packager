<?php

namespace AntonioPrimera\LaraPackager\Components;

class FileManager
{
	public static function createFromStub(string $stubPath, string $destinationPath, array $replace = [])
	{
		$contents = file_get_contents($stubPath);
		foreach ($replace as $key => $value)
			$contents = str_replace($key, $value, $contents);
		
		file_put_contents($destinationPath, $contents);
	}
}