<?php

namespace AntonioPrimera\LaraPackager\Support;

class Paths
{
	
	public static function path(...$parts)
	{
		$cleanParts = [];
		$absolutePath = ($parts[0][0] ?? '/') === '/';
		
		foreach ($parts as $part)
			$cleanParts[] = trim($part, '/');
		
		return ($absolutePath ? '/' : '') . implode(DIRECTORY_SEPARATOR, $cleanParts);
	}
	
	public static function rootPath($path = null)
	{
		return $path ?: getcwd();
	}
}