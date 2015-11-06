<?php

namespace Brosland\Media\Storages;

use Brosland\Media\IFile,
	Brosland\Media\IImageFormat,
	Brosland\Media\IImageFormatProvider,
	Nette\InvalidStateException;

class ImageStorage extends FileStorage implements \Brosland\Media\IImageStorage
{

	/**
	 * @var string
	 */
	private $cacheStoragePath;
	/**
	 * @var IImageFormatProvider
	 */
	private $imageFormatProvider;


	/**
	 * @param string $storagePath
	 * @param string $cacheStoragePath
	 * @param IImageFormatProvider $imageFormatProvider
	 */
	public function __construct($storagePath, $cacheStoragePath,
		IImageFormatProvider $imageFormatProvider)
	{
		parent::__construct($storagePath);

		$this->cacheStoragePath = $cacheStoragePath;
		$this->imageFormatProvider = $imageFormatProvider;
	}

	/**
	 * @param string $source
	 * @param IFile $file
	 * @return string path to file in storage
	 */
	public function save($source, IFile $file)
	{
		$path = parent::save($source, $file);

		$this->fixImageOrientation($path);

		return $path;
	}

	/**
	 * @param IFile $image
	 * @param IImageFormat $imageFormat
	 * @return string path to file in storage
	 */
	public function getPath(IFile $image, IImageFormat $imageFormat = NULL)
	{
		if (!$imageFormat)
		{
			return parent::getPath($image);
		}

		$search = ['/', '<format>', '<month>', '<image>'];
		$replace = [
			DIRECTORY_SEPARATOR,
			$imageFormat->getName(),
			$image->getUploaded()->format('Ym'),
			$image->getName()
		];

		return str_replace($search, $replace, $this->cacheStoragePath);
	}

	/**
	 * @param IFile $image
	 * @param IImageFormat $imageFormat
	 * @return string path to file in assets
	 * @throws InvalidStateException
	 */
	public function createFormatedImage(IFile $image, IImageFormat $imageFormat)
	{
		$path = $this->getPath($image);

		if (!file_exists($path))
		{
			throw new InvalidStateException("File '$path' not found in storage.");
		}

		$formatedImagePath = $this->getPath($image, $imageFormat);
		$dir = dirname($formatedImagePath);

		if (!is_dir($dir) && !@mkdir($dir, 0777, TRUE))
		{
			throw new InvalidStateException("Can not create dir '$dir'.");
		}

		if (!copy($path, $formatedImagePath))
		{
			throw new InvalidStateException("Can not copy image to '$formatedImagePath'.");
		}

		$imageFormat->apply($image, $formatedImagePath);

		return $formatedImagePath;
	}

	/**
	 * @param IFile $image
	 * @param IImageFormat[] $imageFormats if is NULL all image formats will be removed
	 */
	public function removeFormatedImages(IFile $image, array $imageFormats = [])
	{
		if (empty($imageFormats))
		{
			$imageFormats = $this->imageFormatProvider->findAll();
		}

		foreach ($imageFormats as $imageFormat)
		{
			$path = $this->getPath($image, $imageFormat);

			if (file_exists($path))
			{
				@unlink($path);
			}

			$dir = dirname($path);

			if (file_exists($dir) && $this->isDirEmpty($dir))
			{
				rmdir($dir);
			}
		}
	}

	/**
	 * @param IFile $image
	 */
	public function remove(IFile $image)
	{
		parent::remove($image);

		$this->removeFormatedImages($image);
	}

	/**
	 * @param string $path
	 */
	private function fixImageOrientation($path)
	{
		$exif = exif_read_data($path);

		if (!empty($exif['Orientation']))
		{
			$image = \Nette\Utils\Image::fromFile($path);

			switch ($exif['Orientation'])
			{
				case 8:
					$image = $image->rotate(90, 0);
					break;
				case 3:
					$image = $image->rotate(180, 0);
					break;
				case 6:
					$image = $image->rotate(-90, 0);
					break;
			}

			$image->save($path);
		}
	}
}