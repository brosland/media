<?php

namespace Brosland\Media;

interface IImageStorage extends \Brosland\Media\IFileStorage
{

	/**
	 * @param IFile $image
	 * @param IImageFormat $imageFormat
	 * @return string path to file in storage
	 */
	public function createFormatedImage(IFile $image, IImageFormat $imageFormat);
}