<?php

namespace Brosland\Media\Models;

use Brosland\Media\IFileStorage,
	Brosland\Media\IImageStorage,
	Doctrine\ORM\Event\LifecycleEventArgs,
	Doctrine\ORM\Events;

class MediaEventSubscriber extends \Brosland\Models\EventSubscriber
{
	/**
	 * @var \Brosland\Media\IFileStorage $fileStorage
	 */
	private $fileStorage;
	/**
	 * @var \Brosland\Media\IImageStorage $imageStorage
	 */
	private $imageStorage;


	/**
	 * @param \Brosland\Media\IFileStorage $fileStorage
	 * @param \Brosland\Media\IImageStorage $imageStorage
	 */
	public function __construct(IFileStorage $fileStorage, IImageStorage $imageStorage)
	{
		$this->fileStorage = $fileStorage;
		$this->imageStorage = $imageStorage;
	}

	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs
	 */
	public function postPersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if (!$entity instanceof FileEntity)
		{
			return;
		}

		$entity->setOrdering($entity->getId());
		$this->getDao($args)->save($entity);

		if ($entity instanceof ImageEntity)
		{
			$this->imageStorage->save($entity->getFileUpload()->getTemporaryFile(), $entity);
		}
		else
		{
			$this->fileStorage->save($entity->getFileUpload()->getTemporaryFile(), $entity);
		}
	}

	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs
	 */
	public function preRemove(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if ($entity instanceof ImageEntity)
		{
			$this->imageStorage->remove($entity);
		}
		else if ($entity instanceof FileEntity)
		{
			$this->fileStorage->remove($entity);
		}
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array (Events::postPersist, Events::preRemove);
	}
}