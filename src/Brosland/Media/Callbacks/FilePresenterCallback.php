<?php

namespace Brosland\Media\Callbacks;

use Brosland\Media\IFileStorage,
	Kdyby\Doctrine\EntityDao,
	Nette\Application\Responses\FileResponse;

class FilePresenterCallback extends \Nette\Object implements \Brosland\Media\IFileCallback
{

	/**
	 * @var EntityDao
	 */
	private $fileDao;
	/**
	 * @var IFileStorage
	 */
	private $storage;


	/**
	 * @param EntityDao $fileDao
	 * @param IFileStorage $storage
	 */
	public function __construct(EntityDao $fileDao, IFileStorage $storage)
	{
		$this->fileDao = $fileDao;
		$this->storage = $storage;
	}

	/**
	 * @param string $fileName
	 * @return FileResponse
	 */
	public function __invoke($fileName)
	{
		$file = $this->fileDao->findOneBy(['name' => $fileName]);

		if (!$file)
		{
			throw new \Nette\Application\BadRequestException('File not found', 404);
		}

		$path = $this->storage->getPath($file);

		if (!file_exists($path))
		{
			throw new \Nette\Application\BadRequestException('File not found.', 404);
		}

		$fileName = \Nette\Utils\Strings::webalize($file->getLabel()) . '.' . $file->getExt();

		return new FileResponse($path, $fileName, $file->getContentType());
	}
}