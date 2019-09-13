<?php

declare(strict_types=1);

namespace Simplette\Loader\Style;

use Nette\Utils\Finder;
use Simplette\Loader\DepsFinder;


class StyleDepsFinder implements DepsFinder
{

	public function find(string $file): array
	{
		$dependencies = [];
		/** @var \SplFileInfo[] $files */
		$files = Finder::find('*.scss', '*.css')->from(dirname($file));
		foreach ($files as $fileInfo) {
			$dependencies[] = $fileInfo->getPathname();
		}

		return $dependencies;
	}

}
