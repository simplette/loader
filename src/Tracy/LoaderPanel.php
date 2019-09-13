<?php

namespace Simplette\Loader\Tracy;

use Nette\DI\Container;
use Simplette\Loader\Loader;
use Tracy\IBarPanel;


class LoaderPanel implements IBarPanel
{
	/** @var Container */
	private $container;

	/** @var bool */
	private $collected = FALSE;

	/** @var Loader[][] */
	private $loaders = [];

	/** @var bool */
	private $compiled = FALSE;

	/** @var float */
	private $elapsedTime = 0;

	/** @var int */
	private $filesCount = 0;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function getTab(): string
	{
		$this->collectStatistics();

		ob_start();
		require __DIR__ . '/templates/LoaderPanel.tab.phtml';

		return ob_get_clean() ?: '';
	}

	public function getPanel(): string
	{
		$this->collectStatistics();

		ob_start();
		require __DIR__ . '/templates/LoaderPanel.panel.phtml';

		return ob_get_clean() ?: '';
	}

	private function collectStatistics(): void
	{
		if ($this->collected) {
			return;
		}

		foreach ($this->container->findByType(Loader::class) as $name) {
			$category = explode('.', $name)[1];
			/** @var Loader $loader */
			$loader = $this->container->getService($name);
			$this->loaders[$category][] = $loader;
			$this->elapsedTime += $loader->getCompleteTime();

			foreach ($loader->getStatistics() as $statistics) {
				$this->filesCount++;
				if (!empty($statistics['time'])) {
					$this->compiled = TRUE;
				}
			}
		}

		$this->collected = TRUE;
	}
}
