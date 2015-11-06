<?php

namespace Brosland\Media;

interface IImageCallback
{

	/**
	 * @param string $imageName
	 * @param string $imageFormatName
	 */
	public function __invoke($imageName, $imageFormatName);
}