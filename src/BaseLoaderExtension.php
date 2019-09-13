<?php

declare(strict_types=1);

namespace Simplette\Loader;

use Nette\Caching\Cache;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\Statement;
use Nette\IOException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Nette\Utils\ArrayHash;
use Simplette\Loader\Common\NoDepsFinder;
use Simplette\Loader\Tracy\LoaderPanel;

/**
 * Class BaseLoaderExtension
 *
 * @package Simplette\Loader
 *
 * @property-read ArrayHash $config
 */
abstract class BaseLoaderExtension extends CompilerExtension
{
	private const NAME_TRACY_PANEL = 'loader.tracy';

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'debugger' => Expect::bool(FALSE),
			'compiler' => Expect::array(),
			'genDir' => Expect::string('assets/gen'),
			'files' => Expect::array(),
		]);
	}

	public function getConfig(): ArrayHash
	{
		return ArrayHash::from((array) $this->config);
	}

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$wwwDir = $builder->parameters['wwwDir'];
		$genDir = $this->config->genDir;

		if (!is_writable("$wwwDir/$genDir")) {
			throw new IOException("Directory '$wwwDir/$genDir' is not writable.");
		}
	}

	protected function registerLoader(string $extension, string $loaderCompiler, string $dependencyFinder = NoDepsFinder::class): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition("loader.{$this->name}.cache")
			->setType(Cache::class)
			->setArgument('namespace', "loader.{$this->name}")
			->setAutowired(FALSE);

		$builder->addDefinition("loader.{$this->name}.deps")
			->setType($dependencyFinder)
			->setAutowired(FALSE);

		$builder->addDefinition("loader.{$this->name}.files")
			->setType(FilesProvider::class)
			->setArguments([
				'debugMode' => $builder->parameters['debugMode'],
				'categories' => $this->config->files,
				'dependencyFinder' => new Statement("@loader.{$this->name}.deps"),
				'cache' => new Statement("@loader.{$this->name}.cache"),
			])
			->setAutowired(FALSE);

		$builder->addDefinition("loader.{$this->name}.path")
			->setType(PathCompiler::class)
			->setArguments([
				'debugMode' => $builder->parameters['debugMode'],
				'genDir' => $this->config->genDir,
				'extension' => $extension,
				'filesProvider' => new Statement("@loader.{$this->name}.files"),
				'cache' => new Statement("@loader.{$this->name}.cache"),
			])
			->setAutowired(FALSE);

		$service = $builder->addDefinition("loader.{$this->name}")
			->setType(Loader::class)
			->setArguments([
				'debugMode' => $builder->parameters['debugMode'],
				'wwwDir' => $builder->parameters['wwwDir'],
				'loaderCompiler' => new Statement($loaderCompiler, $this->config->compiler ?? []),
				'pathCompiler' => new Statement("@loader.{$this->name}.path"),
				'filesProvider' => new Statement("@loader.{$this->name}.files"),
			])
			->setAutowired(FALSE);

		if ($this->config->debugger) {
			if (!$builder->hasDefinition(self::NAME_TRACY_PANEL)) {
				$builder->addDefinition(self::NAME_TRACY_PANEL)
					->setType(LoaderPanel::class);
			}

			$service->addSetup('@Tracy\Bar::addPanel', ['@' . self::NAME_TRACY_PANEL, self::NAME_TRACY_PANEL]);
		}
	}

	protected function registerMacros(string $class): void
	{
		/** @var FactoryDefinition $latteFactory */
		$latteFactory = $this->getContainerBuilder()->getDefinition('latte.latteFactory');
		$latteFactory
			->getResultDefinition()
			->addSetup('?->onCompile[] = function ($engine) { ' . $class . '::install($engine->getCompiler()); }', ['@self']);
	}

}
