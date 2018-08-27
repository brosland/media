<?php

namespace Brosland\Media\Callbacks;

use Brosland\Media\IImageFormatProvider,
	Brosland\Media\IImageProvider,
	Brosland\Media\IImageStorage,
	Nette\Application\BadRequestException,
	Nette\Utils\Image;

class ImagePresenterCallback implements \Brosland\Media\IImageCallback
{

	/**
	 * @var IImageProvider
	 */
	private $imageProvider;
	/**
	 * @var IImageFormatProvider
	 */
	private $imageFormatProvider;
	/**
	 * @var IImageStorage
	 */
	private $storage;


	/**
	 * @param IImageProvider $imageProvider
	 * @param IImageFormatProvider $imageFormatProvider
	 * @param IImageStorage $storage
	 */
	public function __construct(IImageProvider $imageProvider,
		IImageFormatProvider $imageFormatProvider, IImageStorage $storage)
	{
		$this->imageProvider = $imageProvider;
		$this->imageFormatProvider = $imageFormatProvider;
		$this->storage = $storage;
	}

	/**
	 * @param string $imageName
	 * @param string $imageFormatName
	 * @throws BadRequestException
	 */
	public function __invoke($imageName, $imageFormatName)
	{
		$image = $this->imageProvider->findOneByName($imageName);

		if (!$image)
		{
			throw new BadRequestException('Image not found.', 404);
		}

		$imageFormat = $this->imageFormatProvider->findOneByName($imageFormatName);

		if (!$imageFormat)
		{
			throw new BadRequestException('Image format not found.', 404);
		}

		$originalImagePath = $this->storage->getPath($image);

		if (!file_exists($originalImagePath))
		{
			\Tracy\Debugger::log(new \Nette\InvalidStateException('File not found in the storage.'));

			throw new BadRequestException('Image not found.', 404);
		}

		$path = $this->storage->getPath($image, $imageFormat);

		if (!file_exists($path))
		{
			$this->storage->createFormatedImage($image, $imageFormat);
		}

		Image::fromFile($path)->send();
	}
}