<?php

namespace Brosland\Media\Model;

use Kdyby\Doctrine\EntityDao;

class FileProvider extends \Nette\Object implements \Brosland\Media\IFileProvider
{

	/**
	 * @var EntityDao
	 */
	private $fileDao;


	/**
	 * @param EntityDao $fileDao
	 */
	public function __construct(EntityDao $fileDao)
	{
		$this->fileDao = $fileDao;
	}

	/**
	 * @param string $name
	 * @return \Brosland\Media\IFile
	 */
	public function findOneByName($name)
	{
		$parts = explode('-', $name, 2);

		if (count($parts) < 2)
		{
			return NULL;
		}

		return $this->fileDao->find((int) $parts[0]);
	}
}