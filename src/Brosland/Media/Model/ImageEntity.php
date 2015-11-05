<?php

namespace Brosland\Media\Model;

use Doctrine\ORM\Mapping as ORM,
	Nette\Http\FileUpload;

/**
 * @ORM\Entity
 */
class ImageEntity extends FileEntity
{

	/**
	 * @param FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload)
	{
		if (!$fileUpload->isImage())
		{
			throw new \Nette\InvalidArgumentException("Unsupported image type '{$fileUpload->getContentType()}'.");
		}

		parent::__construct($fileUpload);
	}
}