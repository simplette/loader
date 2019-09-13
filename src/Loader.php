<?php

declare(strict_types=1);

namespace Simplette\Loader;


class Loader
{
	/** @var bool */
	private $debugMode;

	/** @var string */
	private $wwwDir;

	/** @var AssetsCompiler */
	private $loaderCompiler;

	/** @var PathCompiler */
	private $pathCompiler;

	/** @var FilesProvider */
	private $filesProvider;

	/** @var array */
	private $statistics = [];

	/** @var float */
	private $completeTime = 0.0;

	public function __construct(bool $debugMode, string $wwwDir, AssetsCompiler $loaderCompiler, PathCompiler $pathCompiler, FilesProvider $filesProvider)
	{
		$this->debugMode = $debugMode;
		$this->wwwDir = $wwwDir;
		$this->loaderCompiler = $loaderCompiler;
		$this->pathCompiler = $pathCompiler;
		$this->filesProvider = $filesProvider;
	}

	public function getStatistics(): array
	{
		return $this->statistics;
	}

	public function getCompleteTime(): float
	{
		return $this->completeTime;
	}

	public function link(string $name): string
	{
		$startComplete = microtime(TRUE);
		$path = $this->pathCompiler->getPath($name);
		$genFile = "{$this->wwwDir}/$path";

		if (!file_exists($genFile)) {
			$start = microtime(TRUE);
			$this->loaderCompiler->compile($this->filesProvider->getFiles($name), $genFile);
			if ($this->debugMode) {
				$this->statistics[$name]['time'] = microtime(TRUE) - $start;
			}
		}
		if ($this->debugMode) {
			$files = $this->filesProvider->getFiles($name);
			$this->statistics[$name]['size'] = filesize($genFile);
			$this->statistics[$name]['file'] = count($files) > 1 ? $files : reset($files);
			$this->statistics[$name]['date'] = filemtime($genFile);
			$this->statistics[$name]['path'] = $path;
		}

		$this->completeTime = microtime(TRUE) - $startComplete;

		return $path;
	}

}
