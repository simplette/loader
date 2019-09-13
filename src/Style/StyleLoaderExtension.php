<?php

declare(strict_types=1);

namespace Simplette\Loader\Style;

use Simplette\Loader\BaseLoaderExtension;


class StyleLoaderExtension extends BaseLoaderExtension
{
	private const FILE_EXT = 'css';

	public function beforeCompile(): void
	{
		$this->registerLoader(self::FILE_EXT, StyleAssetsCompiler::class, StyleDepsFinder::class);
		$this->registerMacros(StyleLoaderMacros::class);
	}

}
