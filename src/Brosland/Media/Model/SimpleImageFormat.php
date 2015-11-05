<?php

namespace Brosland\Media\Model;

use Brosland\Media\IFile,
	Nette\Utils\Image;

class SimpleImageFormat extends \Nette\Object implements \Brosland\Media\IImageFormat
{

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var int
	 */
	private $width, $height;
	/**
	 * @var bool
	 */
	private $crop, $resizeSmallPicture = FALSE;


	/**
	 * @param string $name
	 */
	public function __construct($name, $width = 800, $height = 600, $crop = FALSE)
	{
		$this->name = $name;
		$this->width = $width;
		$this->height = $height;
		$this->crop = $crop;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param int $width
	 * @return self
	 */
	public function setWidth($width)
	{
		$this->width = $width;

		return $this;
	}

	/**
	 * @param int $height
	 * @return self
	 */
	public function setHeight($height)
	{
		$this->height = $height;

		return $this;
	}

	/**
	 * @param bool $crop
	 * @return self
	 */
	public function setCrop($crop)
	{
		$this->crop = $crop;

		return $this;
	}

	/**
	 * @param bool $resizeSmallPicture
	 * @return self
	 */
	public function setResizeSmallPicture($resizeSmallPicture)
	{
		$this->resizeSmallPicture = $resizeSmallPicture;

		return $this;
	}

	/**
	 * @param IFile $file
	 * @param string $path
	 * @throws \Nette\Utils\UnknownImageFileException
	 */
	public function apply(IFile $file, $path)
	{
		$image = Image::fromFile($path);

		if ($image->getWidth() > $this->width || $image->getHeight() > $this->height || $this->resizeSmallPicture)
		{
			$image->resize($this->width, $this->height, $this->crop ? Image::EXACT : Image::FIT);
		}

		$image->save($path, 85);
	}
}