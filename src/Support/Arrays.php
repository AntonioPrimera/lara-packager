<?php

namespace AntonioPrimera\LaraPackager\Support;

class Arrays
{
	public static function set(array &$arr, string|array $dotSeparatedPath, $value = [])
	{
		if (!$dotSeparatedPath)
			return;
		
		//separate the path into segments
		$pathSegments = is_string($dotSeparatedPath)
			? array_filter(explode('.', $dotSeparatedPath))
			: $dotSeparatedPath;
		$firstSegment = array_shift($pathSegments);
		
		//if we're at the last segment, just set the value
		if (!$pathSegments) {
			$arr[$firstSegment] = $value;
			return;
		}
		
		//create the current segment as an array if it's not already
		if (!(isset($arr[$firstSegment]) && is_array($arr[$firstSegment])))
			$arr[$firstSegment] = [];
		
		static::set($arr[$firstSegment], $pathSegments, $value);
	}
	
	public static function get(array $arr, string|array $dotSeparatedPath, $default = null)
	{
		if (!$dotSeparatedPath)
			return $default;
		
		$pathSegments = is_string($dotSeparatedPath)
			? array_filter(explode('.', $dotSeparatedPath))
			: $dotSeparatedPath;
		
		$currentLevel = $arr;
		foreach ($pathSegments as $pathSegment) {
			$currentLevel = $currentLevel[$pathSegment] ?? $default;
		}
		
		return $currentLevel;
	}
	
	public static function push(array &$arr, string|array $dotSeparatedPath, $value)
	{
		static::set(
			$arr,
			$dotSeparatedPath,
			array_merge(
				static::get($arr, $dotSeparatedPath, []),
				[$value]
			)
		);
	}
}