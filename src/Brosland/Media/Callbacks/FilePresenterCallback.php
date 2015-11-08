<?php

namespace Brosland\Media\Callbacks;

use Brosland\Media\IFileStorage,
 Brosland\Media\IFileProvider,
	Nette\Application\BadRequestException,
	Nette\Application\Responses\FileResponse;

class FilePresenterCallback extends \Nette\Object implements \Brosland\Media\IFileCallback
{

	/**
	 * @var EntityDao
	 */
	private $fileProvider;
	/**
	 * @var IFileStorage
	 */
	private $storage;


	/**
	 * @param IFileProvider $fileProvider
	 * @param IFileStorage $storage
	 */
	public function __construct(IFileProvider $fileProvider, IFileStorage $storage)
	{
		$this->fileProvider = $fileProvider;
		$this->storage = $storage;
	}

	/**
	 * @param string $fileName
	 * @return FileResponse
	 * @throws BadRequestException
	 */
	public function __invoke($fileName)
	{
		$file = $this->fileProvider->findOneByName($fileName);

		if (!$file)
		{
			throw new BadRequestException('File not found', 404);
		}

		$path = $this->storage->getPath($file);

		if (!file_exists($path))
		{
			\Tracy\Debugger::log(new \Nette\InvalidStateException('File not found in the storage.'));

			throw new BadRequestException('File not found.', 404);
		}

		$fileName = \Nette\Utils\Strings::webalize($file->getLabel()) . '.' . $file->getExt();

		return new FileResponse($path, $fileName, $file->getContentType());
	}
}