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
	
	/**
	 * The root path of antonioprimera/lara-packager
	 *
	 * @param string|null $path
	 *
	 * @return string
	 */
	public static function rootPath(...$parts)
	{
		return static::path(dirname(__DIR__, 2), ...$parts); //$path ?: dirname(__DIR__, 2);
	}
	
	/**
	 * The root path of the package to be developed
	 *
	 * @return false|string
	 */
	public static function packageRootPath(...$parts)
	{
		return static::path(getcwd(), ...$parts);
	}
	
	
	//--- Specific paths ----------------------------------------------------------------------------------------------
	
	public static function stubPath(string $stubName)
	{
		return static::rootPath('src/stubs/', $stubName); //static::path(static::rootPath(), 'src/stubs/' . $stubName);
	}
}