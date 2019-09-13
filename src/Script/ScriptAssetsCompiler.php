<?php

declare(strict_types=1);

namespace Simplette\Loader\Script;

use JSMin\JSMin;
use Nette\Utils\Strings;
use Simplette\Loader\AssetsCompiler;


class ScriptAssetsCompiler implements AssetsCompiler
{
	/** @var bool */
	private $minify;

	public function __construct(bool $minify)
	{
		$this->minify = $minify;
	}

	public function compile(array $sourceFiles, string $outputFile): void
	{
		$file = fopen($outputFile, 'wb');
		if (is_resource($file)) {
			foreach ($sourceFiles as $sourceFile) {
				$fileCode = rtrim(file_get_contents($sourceFile) ?: '', " \t\n\r\0\x0B;");
				fwrite($file, (!$this->minify || Strings::endsWith($sourceFile, '.min.js') ? $fileCode : JSMin::minify($fileCode)) . ";\n");
				/** @noinspection DisconnectedForeachInstructionInspection */
				fflush($file);
			}
			fclose($file);
		}
	}

}
