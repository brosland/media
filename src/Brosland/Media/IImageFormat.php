<?php

namespace Brosland\Media;

interface IImageFormat
{

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param IFile $file
	 * @param string $path path to image
	 */
	public function apply(IFile $file, $path);
}