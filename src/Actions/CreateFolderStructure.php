<?php

namespace AntonioPrimera\LaraPackager\Actions;

use Symfony\Component\Console\Output\OutputInterface;

class CreateFolderStructure
{
	
	public static function run(string $rootPath, OutputInterface $output, $verbose)
	{
		$folders = [
			'src',
			'tests',
			'tests' . DIRECTORY_SEPARATOR . 'Unit',
			'tests' . DIRECTORY_SEPARATOR . 'Feature',
			'tests' . DIRECTORY_SEPARATOR . 'TestContext',
		];
		
		foreach ($folders as $folder) {
			$fullPath = $rootPath . DIRECTORY_SEPARATOR . $folder;
			if (!is_dir($fullPath)) {
				mkdir($fullPath);
				if ($verbose)
					$output->writeln('Created folder: ' . $fullPath);
			} else {
				if ($verbose)
					$output->writeln('Folder already exists: ' . $fullPath);
			}
		}
	}
}