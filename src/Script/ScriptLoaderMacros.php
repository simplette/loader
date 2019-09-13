<?php

declare(strict_types=1);

namespace Simplette\Loader\Script;

use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;


class ScriptLoaderMacros extends MacroSet
{

	/**
	 * @param Compiler $compiler
	 *
	 * @return static
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('script', [$me, 'macroStyle'], NULL, [$me, 'macroAttrStyle']);

		return $me;
	}


	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 * @throws CompileException
	 */
	public function macroStyle(MacroNode $node, PhpWriter $writer): string
	{
		return $writer->write("echo %escape(\$basePath . '/' . \$presenter->context->getService('loader.script')->link(%node.word));");
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 * @throws CompileException
	 */
	public function macroAttrStyle(MacroNode $node, PhpWriter $writer): string
	{
		return $writer->write("echo ' src=\"' . %escape(\$basePath . '/' . \$presenter->context->getService('loader.script')->link(%node.word)) . '\"';");
	}

}
