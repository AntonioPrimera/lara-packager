<?php

namespace AntonioPrimera\LaraPackager\Components;

use AntonioPrimera\LaraPackager\Support\Arrays;
use AntonioPrimera\LaraPackager\Support\Paths;
use Exception;

class ComposerJson
{
	const DEFAULT_TYPES = ['library', 'project', 'metapackage', 'composer-plugin'];
	
	protected $contents = [];
	protected $filePath = null;
	
	public function __construct(?string $path = null)
	{
		if ($path)
			$this->readFromFile($path);
	}
	
	/**
	 * Read the composer.json file into the current instance. The $path
	 * argument can be the folder where the composer.json file
	 * is located or the path including the file name
	 *
	 * @param string $path
	 *
	 * @return ComposerJson
	 */
	public function readFromFile(string $path) : static
	{
		$this->setFilePath($path);
		
		if (!$this->fileExists()) {
			$this->contents = [];
			return $this;
		}
		
		$fileContents = file_get_contents($this->filePath);
		$this->contents = json_decode($fileContents, true);
		
		return $this;
	}
	
	/**
	 * Write the contents of the given instance contents to composer.json
	 * Optionally, back up the original composer.json file
	 *
	 * @param bool $backup
	 */
	public function writeFile(bool $backup = true)
	{
		//if we already have a composer.json file, optionally back it up as 'composer.json.bak'
		if ($backup && file_exists($this->filePath))
			copy($this->filePath, $this->filePath . '.bak');
		
		//write the contents to the file
		file_put_contents(
			$this->filePath,
			json_encode($this->contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
		);
		
		return $this;
	}
	
	//--- Content setters ---------------------------------------------------------------------------------------------
	
	public function setName($name)
	{
		$this->contents['name'] = strtolower($name);	//todo: add kebab-case transformation
		return $this;
	}
	
	public function setDescription($description)
	{
		$this->contents['description'] = $description;
		return $this;
	}
	
	public function setType($type, $allowCustomType = false)
	{
		if ($allowCustomType || in_array($type, static::DEFAULT_TYPES))
			$this->contents['type'] = $type;
		
		return $this;
	}
	
	public function setLicense($license)
	{
		$this->contents['license'] = $license;
		return $this;
	}
	
	public function setAuthor($name, $email)
	{
		Arrays::set($this->contents, ['authors'][0], compact('name', 'email'));
		return $this;
	}
	
	public function addAuthor($name, $email)
	{
		Arrays::push($this->contents, 'authors', compact('name', 'email'));
		return $this;
	}
	
	public function setMinimumStability($stability)
	{
		$this->contents['minimum-stability'] = $stability;
		return $this;
	}
	
	public function setExtra($key, $value)
	{
		Arrays::set($this->contents, ['extra', $key], $value);
		return $this;
	}
	
	//--- Required packages -------------------------------------------------------------------------------------------
	
	public function addRequired($packageName, $version, $dev = false)
	{
		Arrays::set($this->contents, [$dev ? 'require-dev' : 'require', $packageName], $version);
		return $this;
	}
	
	public function removeRequired($packageName, $dev)
	{
		//todo: implement this
	}
	
	//--- Autoload paths ----------------------------------------------------------------------------------------------
	
	public function addPsr4Autoload($namespace, $path, $dev = false)
	{
		return $this->addPsrAutoload($namespace, $path, $dev, 4);
	}
	
	public function addPsr0Autoload($namespace, $path, $dev = false)
	{
		return $this->addPsrAutoload($namespace, $path, $dev, 0);
	}
	
	public function addFileAutoload($path, $dev = false)
	{
		$this->addFileOrClassmapAutoload($path, $dev,'files');
	}
	
	public function addClassmapAutoload($path, $dev = false)
	{
		$this->addFileOrClassmapAutoload($path, $dev,'classmap');
	}
	
	//--- Getters & Setters -------------------------------------------------------------------------------------------
	
	/**
	 * @return null
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}
	
	/**
	 * @param null $path
	 */
	public function setFilePath($path) : static
	{
		$this->filePath = $this->makeFilePath($path);
		return $this;
	}
	
	public function fileExists() : bool | null
	{
		return $this->filePath ? file_exists($this->filePath) : null;
	}
	
	public function getContents()
	{
		return $this->contents;
	}
	
	//--- Special getters ---------------------------------------------------------------------------------------------
	
	public function get($dotSeparatedPath, $default)
	{
		return Arrays::get($this->contents, $dotSeparatedPath, $default);
	}
	
	public function requires($package)
	{
		return array_key_exists($package, $this->contents['require'] ?? []);
	}
	
	public function requiresDev($package)
	{
		return array_key_exists($package, $this->contents['require-dev'] ?? []);
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function makeFilePath(string $path) : string
	{
		return stripos($path, 'composer.json') === false
			? Paths::path($path, 'composer.json')
			: $path;
	}
	
	protected function addPsrAutoload($namespace, $path, $dev, $psr)
	{
		if (!($psr === 0 || $psr === 4))
			return $this;
		
		Arrays::set(
			$this->contents,
			[$dev ? 'autoload-dev' : 'autoload', 'psr-4', $namespace],
			rtrim($path, '/') . '/'							//add a trailing slash
		);
		return $this;
	}
	
	protected function addFileOrClassmapAutoload(string $fileName, $dev, $filesOrClassMap)
	{
		if (!in_array($filesOrClassMap, ['files', 'classmap']))
			return $this;
		
		$autoload = $dev ? 'autoload-dev' : 'autoload';
		$list = $this->contents[$autoload][$filesOrClassMap] ?? [];
		if (!is_array($list))
			$list = [];
		
		if (!in_array($fileName, $list)) {
			$list[] = $fileName;
			$this->contents[$autoload][$filesOrClassMap] = $list;
		}
		
		return $this;
	}
}