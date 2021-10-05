<?php

namespace AntonioPrimera\LaraPackager\Support;

class Namespaces
{
	
	public static function create(...$parts)
	{
		$cleanParts = [];
		
		foreach ($parts as $part) {
			//we replace '/' with '\\' in case you accidentally type it wrong
			$partSegments = array_filter(explode('\\', str_replace('/', '\\', $part)));
			$cleanParts = array_merge($cleanParts, $partSegments);
		}
		
		return implode('\\', $cleanParts);
	}
	
	public static function createForComposerAutoload(...$parts)
	{
		return static::create(...$parts) . '\\';
	}
}