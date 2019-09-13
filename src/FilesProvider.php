<?php

declare(strict_types=1);

namespace Simplette\Loader;

use Nette\Caching\Cache;
use Nette\Utils\Arrays;


class FilesProvider
{
	/** @var bool */
	private $debugMode;

	/** @var string[][] */
	private $categories;

	/** @var DepsFinder */
	private $dependencyFinder;

	/** @var Cache */
	private $cache;

	public function __construct(bool $debugMode, array $categories, DepsFinder $dependencyFinder, Cache $cache)
	{
		$this->debugMode = $debugMode;
		$this->categories = $categories;
		$this->dependencyFinder = $dependencyFinder;
		$this->cache = $cache;
	}

	public function getFiles(string $name): array
	{
		return $this->categories[$name];
	}

	public function getFilesWithDependencies(string $name): array
	{
		$dependencies = $this->cache->load($this->getCacheKey($name));
		if ($dependencies === NULL) {
			$dependencies = [];
			foreach ($this->categories[$name] as $file) {
				$dependencies[] = $file;
				$dependencies[] = $this->dependencyFinder->find($file);
			}
			$dependencies = Arrays::flatten($dependencies);
			$this->updateDependencies($name, $dependencies);
		}

		return $dependencies;
	}

	private function updateDependencies(string $name, array $dependencies): void
	{
		$this->cache->save($this->getCacheKey($name), $dependencies, [
			Cache::FILES => $this->debugMode ? $this->categories[$name] : NULL,
		]);
	}

	private function getCacheKey(string $name): array
	{
		return [FilesProvider::class, $this->debugMode, $name, $this->getFiles($name)];
	}

}
