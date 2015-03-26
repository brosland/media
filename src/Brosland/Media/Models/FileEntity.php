<?php

namespace Brosland\Media\Models;

use Doctrine\ORM\Mapping as ORM,
	Nette\Http\FileUpload;

/**
 * @ORM\Entity
 * @ORM\Table(name="Media_File")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="integer")
 * @ORM\DiscriminatorMap({
 * 	"0" = "Brosland\Media\Models\FileEntity",
 * 	"1" = "Brosland\Media\Models\ImageEntity"
 * })
 * @ORM\HasLifecycleCallbacks
 */
class FileEntity extends \Kdyby\Doctrine\Entities\BaseEntity implements \Brosland\Media\IFile
{
	use \Kdyby\Doctrine\Entities\Attributes\Identifier;

	const SIZE_SCALE_KB = 1,
		SIZE_SCALE_MB = 2,
		SIZE_SCALE_GB = 3;


	/**
	 * @ORM\Column
	 * @var string
	 */
	private $label;
	/**
	 * @ORM\Column
	 * @var string
	 */
	private $name;
	/**
	 * @ORM\Column
	 * @var string
	 */
	private $contentType;
	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $size;
	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	private $uploaded;
	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $ordering = 0;
	/**
	 * @var \Nette\Http\FileUpload
	 */
	private $fileUpload;


	/**
	 * @param \Nette\Http\FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload)
	{
		$this->label = pathinfo($fileUpload->getName(), PATHINFO_FILENAME);
		$this->name = $fileUpload->getSanitizedName();
		$this->contentType = $fileUpload->getContentType();
		$this->size = $fileUpload->getSize();
		$this->uploaded = new \DateTime();
		$this->fileUpload = $fileUpload;
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @param string $label
	 * @return self
	 */
	public function setLabel($label)
	{
		$this->label = $label;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * @return string
	 */
	public function getExt()
	{
		return \Nette\Utils\Strings::lower(pathinfo($this->name, PATHINFO_EXTENSION));
	}

	/**
	 * @return string
	 */
	public function getFullname()
	{
		return $this->id . '-' . $this->name;
	}

	/**
	 * @return \DateTime
	 */
	public function getUploaded()
	{
		return $this->uploaded;
	}

	/**
	 * @param $uploaded \DateTime
	 * @return self
	 */
	public function setUploaded(\DateTime $uploaded)
	{
		$this->uploaded = $uploaded;

		return $this;
	}

	/**
	 * @param int $scale
	 * @return int
	 */
	public function getSize($scale = 0)
	{
		return ceil($this->size / pow(1024, $scale));
	}

	/**
	 * @param int $size file size in bytes
	 * @return self
	 */
	public function setSize($size)
	{
		$this->size = $size;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getOrdering()
	{
		return $this->ordering;
	}

	/**
	 * @param int $ordering
	 * @return self
	 */
	public function setOrdering($ordering)
	{
		$this->ordering = $ordering;

		return $this;
	}

	/**
	 * @return FileUpload
	 */
	public function getFileUpload()
	{
		return $this->fileUpload;
	}
}