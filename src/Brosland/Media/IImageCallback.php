<?php

namespace Brosland\Media;

interface IImageCallback
{

	/**
	 * @param IFile $file
	 * @param IImageFormat $imageFormat
	 */
	public function __invoke(IFile $file, IImageFormat $imageFormat);
}