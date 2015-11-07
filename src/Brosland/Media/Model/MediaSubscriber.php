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
	 * @var FileEntity[]
	 */
	private $deletedFiles = [];


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
	 * @param \Doctrine\ORM\Event\OnFlushEventArgs $args
	 */
	public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $args)
	{
		$entityManager = $args->getEntityManager();

		$entities = $entityManager->getUnitOfWork()->getScheduledEntityDeletions();
		$ids = [];

		foreach ($entities as $entity)
		{
			if ($entity instanceof FileEntity)
			{
				$ids[] = $entity->getId();
			}
		}

		if (!empty($ids))
		{
			$this->deletedFiles = $entityManager->getRepository(FileEntity::class)->findBy(['id' => $ids]);
		}
	}

	/**
	 * @param \Doctrine\ORM\Event\PostFlushEventArgs $args
	 */
	public function postFlush(\Doctrine\ORM\Event\PostFlushEventArgs $args)
	{
		$files = $this->deletedFiles;
		$this->deletedFiles = [];

		foreach ($files as $file)
		{
			if ($file instanceof ImageEntity)
			{
				$this->imageStorage->remove($file);
			}
			else if ($file instanceof FileEntity)
			{
				$this->fileStorage->remove($file);
			}
		}
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return [Events::prePersist, Events::postPersist, Events::onFlush, Events::postFlush];
	}
}