<?php

namespace Brosland\Media\Model;

use Brosland\Media\IFileStorage,
	Brosland\Media\IImageStorage,
	Doctrine\ORM\Event\LifecycleEventArgs,
	Doctrine\ORM\Events;

class MediaSubscriber implements \Kdyby\Events\Subscriber
{

	/**
	 * @var IFileStorage $fileStorage
	 */
	private $fileStorage;
	/**
	 * @var IImageStorage $imageStorage
	 */
	private $imageStorage;


	/**
	 * @param IFileStorage $fileStorage
	 * @param IImageStorage $imageStorage
	 */
	public function __construct(IFileStorage $fileStorage,
		IImageStorage $imageStorage)
	{
		$this->fileStorage = $fileStorage;
		$this->imageStorage = $imageStorage;
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if ($entity instanceof FileEntity && empty($entity->getOrdering()))
		{
			$entity->setOrdering((new \DateTime())->getTimestamp());
		}
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function postPersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if (!$entity instanceof FileEntity)
		{
			return;
		}

		$entity->setName($entity->getId() . '-' . $entity->getName());

		$args->getEntityManager()->persist($entity)
			->flush();

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
	 * @param LifecycleEventArgs $args
	 */
	public function postRemove(LifecycleEventArgs $args)
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
		return [Events::prePersist, Events::postPersist, Events::postRemove];
	}
}