<?php

namespace Simplette\Loader\Style;

use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;


class StyleLoaderMacros extends MacroSet
{

	/**
	 * @param Compiler $compiler
	 *
	 * @return static
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('style', [$me, 'macroStyle'], NULL, [$me, 'macroAttrStyle']);
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
		return $writer->write("echo %escape(\$basePath . '/' . \$presenter->context->getService('loader.style')->link(%node.word));");
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
		return $writer->write("echo ' href=\"' . %escape(\$basePath . '/' . \$presenter->context->getService('loader.style')->link(%node.word)) . '\"';");
	}

}
