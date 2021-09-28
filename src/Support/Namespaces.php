<?php

namespace AntonioPrimera\LaraPackager\Support;

class Namespaces
{
	
	public static function create(...$parts)
	{
		$cleanParts = [];
		
		foreach ($parts as $part) {
			$partSegments = array_filter(explode('\\', $part));
			$cleanParts = array_merge($cleanParts, $partSegments);
		}
			//$cleanParts[] = trim($part, '\\');
		
		return implode('\\', $cleanParts);
	}
	
	public static function createForComposerAutoload(...$parts)
	{
		return static::doubleBackSlashes(static::create(...$parts) . '\\');
	}
	
	public static function doubleBackSlashes(string $ns)
	{
		return str_replace('\\', '\\\\', $ns);
	}
}