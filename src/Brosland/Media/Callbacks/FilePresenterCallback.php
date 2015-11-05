<?php

namespace Brosland\Media\Callbacks;

use Brosland\Media\IFile,
	Brosland\Media\IFileStorage,
	Nette\Application\Responses\FileResponse;

class FilePresenterCallback extends \Nette\Object implements \Brosland\Media\IFileCallback
{

	/**
	 * @var \Brosland\Media\IFileStorage
	 */
	private $storage;


	/**
	 * @param \Brosland\Media\IFileStorage $storage
	 */
	public function __construct(IFileStorage $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * @param \Brosland\Media\IFile $file
	 * @return \Nette\Application\Responses\FileResponse
	 */
	public function __invoke(IFile $file)
	{
		$path = $this->storage->getPath($file);

		if (!file_exists($path))
		{
			throw new \Nette\Application\BadRequestException('File not found.', 404);
		}

		$fileName = \Nette\Utils\Strings::webalize($file->getLabel()) . '.' . $file->getExt();

		return new FileResponse($path, $fileName, $file->getContentType());
	}
}