<?php

namespace Brosland\Media\Models;

use Brosland\Media\IImageFormat;

class ImageFormatProvider extends \Nette\Object implements \Brosland\Media\IImageFormatProvider
{
	/**
	 * @var IImageFormat[]
	 */
	private $imageFormats = array ();


	/**
	 * @param IImageFormat $imageFormat
	 */
	public function add(IImageFormat $imageFormat)
	{
		$this->imageFormats[$imageFormat->getName()] = $imageFormat;
	}

	/**
	 * @param string $name
	 * @return \Brosland\Media\IImageFormat
	 */
	public function findOneByName($name)
	{
		if (!isset($this->imageFormats[$name]))
		{
			return NULL;
		}

		return $this->imageFormats[$name];
	}

	/**
	 * @return \Brosland\Media\IImageFormat[]
	 */
	public function findAll()
	{
		return $this->imageFormats;
	}
}