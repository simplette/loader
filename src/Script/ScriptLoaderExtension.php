<?php

declare(strict_types=1);

namespace Simplette\Loader\Script;

use Simplette\Loader\BaseLoaderExtension;


class ScriptLoaderExtension extends BaseLoaderExtension
{
	private const FILE_EXT = 'js';

	public function beforeCompile(): void
	{
		$this->registerLoader(self::FILE_EXT, ScriptAssetsCompiler::class);
		$this->registerMacros(ScriptLoaderMacros::class);
	}

}
