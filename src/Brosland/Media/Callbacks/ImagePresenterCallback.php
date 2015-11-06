<?php

namespace Brosland\Media\Callbacks;

use Brosland\Media\IImageFormatProvider,
	Brosland\Media\IImageStorage,
	Kdyby\Doctrine\EntityDao,
	Nette\Application\BadRequestException,
	Nette\Utils\Image;

class ImagePresenterCallback extends \Nette\Object implements \Brosland\Media\IImageCallback
{

	/**
	 * @var EntityDao
	 */
	private $imageDao;
	/**
	 * @var IImageFormatProvider
	 */
	private $imageFormatProvider;
	/**
	 * @var IImageStorage
	 */
	private $storage;


	/**
	 * @param EntityDao $imageDao
	 * @param IImageFormatProvider $imageFormatProvider
	 * @param IImageStorage $storage
	 */
	public function __construct(EntityDao $imageDao,
		IImageFormatProvider $imageFormatProvider, IImageStorage $storage)
	{
		$this->imageDao = $imageDao;
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
		$image = $this->imageDao->findOneBy(['name' => $imageName]);

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