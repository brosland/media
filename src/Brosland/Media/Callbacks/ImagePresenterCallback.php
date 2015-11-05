<?php

namespace Brosland\Media\Callbacks;

use Brosland\Media\IFile,
	Brosland\Media\IImageFormat,
	Brosland\Media\IImageStorage,
	Nette\Utils\Image;

class ImagePresenterCallback extends \Nette\Object implements \Brosland\Media\IImageCallback
{

	/**
	 * @var \Brosland\Media\IImageStorage
	 */
	private $storage;


	/**
	 * @param \Brosland\Media\IImageStorage $storage
	 */
	public function __construct(IImageStorage $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * @param IFile $image
	 * @param IImageFormat $imageFormat
	 * @throws \Nette\Application\BadRequestException
	 */
	public function __invoke(IFile $image, IImageFormat $imageFormat)
	{
		$originalImagePath = $this->storage->getPath($image);

		if (!file_exists($originalImagePath))
		{
			throw new \Nette\Application\BadRequestException('Image not found.', 404);
		}

		$path = $this->storage->getPath($image, $imageFormat);

		if (!file_exists($path))
		{
			$this->storage->createFormatedImage($image, $imageFormat);
		}

		Image::fromFile($path)->send();
	}
}