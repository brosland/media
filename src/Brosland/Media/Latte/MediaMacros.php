<?php

namespace Brosland\Media\Latte;

use Latte\Compiler,
	Latte\MacroNode,
	Latte\PhpWriter;

/**
 * Media macros
 *
 * /--code latte
 * {* file *}
 * <a href="{file $file}">Link</a>
 * <a n:fhref="$file">Link</a>
 *
 * {* image *}
 * <a href="{image $image, $format}">Link</a> or <img src="{image $image, $format}">
 * <a href="{img $image, $format}">Link</a> or <img src="{img $image, $format}">
 * <a n:ihref="$image, $format">Link</a>
 * <img n:src="$image, $format">
 * \--
 */
class MediaMacros extends \Latte\Macros\MacroSet
{

	/**
	 * @param \Latte\Compiler $compiler
	 * @return \Latte\Macros\MacroSet
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);
		// file
		$me->addMacro('file', [$me, 'macroFile']);
		// n:fhref
		$me->addMacro('fhref', NULL, NULL, function (MacroNode $node, PhpWriter $writer) use ($me)
		{
			return ' ?> href="<?php ' . $me->macroFile($node, $writer) . ' ?>"<?php ';
		});
		// image
		$me->addMacro('image', [$me, 'macroImage']);
		// img
		$me->addMacro('img', [$me, 'macroImage']);
		// n:src
		$me->addMacro('src', NULL, NULL, function (MacroNode $node, PhpWriter $writer) use ($me)
		{
			return ' ?> src="<?php ' . $me->macroImage($node, $writer) . ' ?>"<?php ';
		});
		// n:ihref
		$me->addMacro('ihref', NULL, NULL, function (MacroNode $node, PhpWriter $writer) use ($me)
		{
			return ' ?> href="<?php ' . $me->macroImage($node, $writer) . ' ?>"<?php ';
		});

		return $me;
	}

	/**
	 * {file ...}
	 * n:fhref
	 *
	 * @param string
	 * @param mixed
	 * @return string
	 * @throws \Latte\CompileException
	 */
	public function macroFile(MacroNode $node, $writer)
	{
		$data = explode(',', $node->args);

		if (count($data) < 1)
		{
			throw new \Latte\CompileException('Invalid arguments count for file macro.');
		}

		foreach ($data as &$value)
		{
			$value = trim($value);
		}

		list($file) = $data;

		return $writer->write("echo %escape(\$_presenter->link('//:Nette:Micro:', ['file'=>"
				. $writer->formatWord("{$file}") . ']))');
	}

	/**
	 * {image ...}
	 * {img ...}
	 * n:src
	 * n:ihref
	 *
	 * @param string
	 * @param mixed
	 * @return string
	 * @throws \Latte\CompileException
	 */
	public function macroImage(MacroNode $node, $writer)
	{
		$data = explode(',', $node->args);

		if (count($data) < 2)
		{
			throw new \Latte\CompileException('Invalid arguments count for image macro.');
		}

		foreach ($data as &$value)
		{
			$value = trim($value);
		}

		list($image, $format) = $data;

		return $writer->write("echo %escape(\$_presenter->link('//:Nette:Micro:', ['image'=>"
				. $writer->formatWord("{$image}") . ",'format'=>" . $writer->formatWord($format) . ']))');
	}
}