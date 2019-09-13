<?php

declare(strict_types=1);

namespace Simplette\Loader;


interface AssetsCompiler
{

	public function compile(array $sourceFiles, string $outputFile): void;

}
