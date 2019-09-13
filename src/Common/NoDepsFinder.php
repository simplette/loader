<?php

declare(strict_types=1);

namespace Simplette\Loader\Common;

use Simplette\Loader\DepsFinder;


class NoDepsFinder implements DepsFinder
{

	public function find(string $file): array
	{
		return [];
	}

}
