<?php

declare(strict_types=1);

namespace Simplette\Loader;


interface DepsFinder
{

	public function find(string $file): array;

}
