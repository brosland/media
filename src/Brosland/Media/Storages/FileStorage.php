<?php

namespace Brosland\Media\Storages;

use Brosland\Media\IFile;

class FileStorage extends \Nette\Object implements \Brosland\Media\IFileStorage
{
	/**
	 * @var string
	 */
	private $storagePath;


	/**
	 * @param string $storagePath
	 */
	public function __construct($storagePath)
	{
		$this->storagePath = $storagePath;
	}

	/**
	 * @param IFile $file
	 * @return string path to file in storage
	 */
	public function getPath(IFile $file)
	{
		return $this->storagePath . DIRECTORY_SEPARATOR . $file->getUploaded()->format('Ym')
			. DIRECTORY_SEPARATOR . $file->getFullname();
	}

	/**
	 * @param string $source
	 * @param IFile $file
	 * @return string path to file in storage
	 * @throws \Nette\InvalidStateException
	 */
	public function save($source, IFile $file)
	{
		$path = $this->getPath($file);
		$dir = dirname($path);

		if (!is_dir($dir) && !@mkdir($dir, 0777, TRUE))
		{
			throw new \Nette\InvalidStateException("Can not create dir '$dir'.");
		}

		if (!rename($source, $path))
		{
			throw new \Nette\InvalidStateException("Can not move file from '$source' to '$path'.");
		}

		return $path;
	}

	/**
	 * @param IFile $file
	 * @throws \Nette\InvalidStateException
	 */
	public function remove(IFile $file)
	{
		$path = $this->getPath($file);

		if (!@unlink($path))
		{
			throw new \Nette\InvalidStateException('File not found.');
		}

		$dir = dirname($path);

		if ($this->isDirEmpty($dir))
		{
			rmdir($dir);
		}
	}

	/**
	 * @param string $dir path to dir
	 * @return bool
	 */
	protected function isDirEmpty($dir)
	{
		if (!is_readable($dir))
		{
			throw new \Nette\InvalidArgumentException("Dir '$dir' not found.");
		}

		return (count(scandir($dir)) == 2);
	}
}