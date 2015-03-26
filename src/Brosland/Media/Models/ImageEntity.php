<?php

namespace Brosland\Media\Models;

use Doctrine\ORM\Mapping as ORM,
	Nette\Http\FileUpload;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ImageEntity extends FileEntity
{

	/**
	 * @param \Nette\Http\FileUpload $fileUpload
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