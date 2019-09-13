<?php

declare(strict_types=1);

namespace Simplette\Loader\Style;

use Nette\Utils\Strings;
use ScssPhp\ScssPhp\Compiler;
use Simplette\Loader\AssetsCompiler;


class StyleAssetsCompiler implements AssetsCompiler
{

	public function compile(array $sourceFiles, string $outputFile): void
	{
		$file = fopen($outputFile, 'wb');
		if (is_resource($file)) {
			foreach ($sourceFiles as $sourceFile) {
				if (Strings::endsWith($sourceFile, '.min.css')) {
					fwrite($file, file_get_contents($sourceFile) . "\n");
				} elseif (Strings::endsWith($sourceFile, '.scss')) {
					$compiler = new Compiler();
					$compiler->addImportPath(dirname($sourceFile));
					fwrite($file, $compiler->compile(file_get_contents($sourceFile) ?: '', $sourceFile) . "\n");
					fflush($file);
				} else {
					fwrite($file, file_get_contents($sourceFile) . "\n");
					fflush($file);
				}
			}
			fclose($file);
		}
	}

}
