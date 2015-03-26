<?php

namespace Brosland\Media\Models;

use Kdyby\Doctrine\EntityDao;

class FileProvider extends \Nette\Object implements \Brosland\Media\IFileProvider
{
	/**
	 * @var \Kdyby\Doctrine\EntityDao
	 */
	private $fileDao;


	/**
	 * @param \Kdyby\Doctrine\EntityDao $fileDao
	 */
	public function __construct(EntityDao $fileDao)
	{
		$this->fileDao = $fileDao;
	}

	/**
	 * @param string $fullname
	 * @return \Brosland\Media\IFile
	 */
	public function findOneByFullname($fullname)
	{
		$parts = explode('-', $fullname, 2);

		if (count($parts) < 2)
		{
			return NULL;
		}

		return $this->fileDao->find((int) $parts[0]);
	}
}