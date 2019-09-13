<?php

declare(strict_types=1);

namespace Simplette\Loader;

use Nette\Caching\Cache;
use Nette\Utils\Strings;


class PathCompiler
{
	/** @var bool */
	private $debugMode;

	/** @var string */
	private $genDir;

	/** @var string */
	private $extension;

	/** @var FilesProvider */
	private $filesProvider;

	/** @var Cache */
	private $cache;

	public function __construct(bool $debugMode, string $genDir, string $extension, FilesProvider $filesProvider, Cache $cache)
	{
		$this->debugMode = $debugMode;
		$this->genDir = $genDir;
		$this->extension = $extension;
		$this->filesProvider = $filesProvider;
		$this->cache = $cache;
	}

	public function getPath(string $name): string
	{
		$path = $this->cache->load($this->getCacheKey($name));
		if ($path === NULL) {
			$path = $this->computePath($name);
			$this->updatePath($name, $path);
		}

		return $path;
	}

	private function computePath(string $name): string
	{
		return "{$this->genDir}/$name.{$this->computeHash($name)}.{$this->extension}";
	}

	private function updatePath(string $name, string $path): void
	{
		$this->cache->save($this->getCacheKey($name), $path, [
			Cache::FILES => $this->debugMode ? $this->filesProvider->getFilesWithDependencies($name) : NULL,
		]);
	}

	private function getCacheKey(string $name): array
	{
		return [PathCompiler::class, $this->debugMode, $name, $this->filesProvider->getFiles($name)];
	}

	private function computeHash(string $name): string
	{
		$debug = $this->debugMode ? 1 : 0;
		$files = implode(',', $this->filesProvider->getFilesWithDependencies($name));
		$time = $this->findLastModifyTime($name);
		$sha1Raw = sha1("$debug;$files;$time", TRUE);
		$base64 = Strings::replace(base64_encode($sha1Raw), '~\W~');

		return Strings::substring(Strings::webalize($base64), 0, 8);
	}

	private function findLastModifyTime(string $name): int
	{
		$time = 0;
		foreach ($this->filesProvider->getFilesWithDependencies($name) as $sourceFile) {
			$time = max($time, filemtime($sourceFile));
		}

		return (int) $time;
	}

}
