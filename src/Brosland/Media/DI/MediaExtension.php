<?php

namespace Brosland\Media\DI;

use Brosland\Media\Models\FileEntity,
	Brosland\Media\Models\ImageEntity,
	Kdyby\Doctrine\DI\IEntityProvider,
	Nette\DI\Statement;

class MediaExtension extends \Nette\DI\CompilerExtension implements IEntityProvider
{
	/**
	 * @var array
	 */
	private static $DEFAULTS = array (
		'fileStorageDir' => '%appDir%/../storage/files',
		'imageStorageDir' => '%appDir%/../storage/images',
		'imagePath' => '%wwwDir%/images',
		'fileRoute' => 'assets/<month>/<file>',
		'imageRoute' => 'images/<format>/<month>/<image>',
		'fileMask' => '<file>',
		'imageMask' => '<image>'
	);


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig(self::$DEFAULTS);

		if (!$builder->hasDefinition($this->prefix('fileProvider')))
		{
			$builder->addDefinition($this->prefix('fileProvider'))
				->setClass(\Brosland\Media\Models\FileProvider::class)
				->setArguments(array (new Statement('@doctrine.dao', array (FileEntity::class))));
		}

		$fileProvider = $builder->getDefinition($this->prefix('fileProvider'));

		if (!$builder->hasDefinition($this->prefix('imageProvider')))
		{
			$builder->addDefinition($this->prefix('imageProvider'))
				->setClass(\Brosland\Media\Models\FileProvider::class)
				->setArguments(array (new Statement('@doctrine.dao', array (ImageEntity::class))));
		}

		$imageProvider = $builder->getDefinition($this->prefix('imageProvider'));

		if (!$builder->hasDefinition($this->prefix('imageFormatProvider')))
		{
			$defaultImageFormat = $builder->addDefinition($this->prefix('defaultImageFormat'))
				->setClass(\Brosland\Media\Models\SimpleImageFormat::class)
				->setArguments(array ('default'));

			$builder->addDefinition($this->prefix('imageFormatProvider'))
				->setClass(\Brosland\Media\Models\ImageFormatProvider::class)
				->setArguments(array (array ($defaultImageFormat)));
		}

		$imageFormatProvider = $builder->getDefinition($this->prefix('imageFormatProvider'));

		$fileStorage = $builder->addDefinition($this->prefix('fileStorage'))
			->setClass(\Brosland\Media\Storages\FileStorage::class)
			->setArguments(array ($builder->expand($config['fileStorageDir'])));

		$imageStorage = $builder->addDefinition($this->prefix('imageStorage'))
			->setClass(\Brosland\Media\Storages\ImageStorage::class)
			->setArguments(array (
				$builder->expand($config['imageStorageDir']),
				$builder->expand($config['imagePath']),
				$imageFormatProvider
			));

		$filePresenterCallback = $builder->addDefinition($this->prefix('filePresenterCallback'))
			->setClass(\Brosland\Media\Callbacks\FilePresenterCallback::class)
			->setArguments(array ($fileStorage))
			->setAutowired(FALSE);

		$imagePresenterCallback = $builder->addDefinition($this->prefix('imagePresenterCallback'))
			->setClass(\Brosland\Media\Callbacks\ImagePresenterCallback::class)
			->setArguments(array ($imageStorage))
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('fileRoute'))
			->setClass(\Brosland\Media\Routes\FileRoute::class)
			->setArguments(array (
				$config['fileRoute'],
				$fileProvider,
				$filePresenterCallback,
				$config['fileMask']
			))->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('imageRoute'))
			->setClass(\Brosland\Media\Routes\ImageRoute::class)
			->setArguments(array (
				$config['imageRoute'],
				$imageProvider,
				$imageFormatProvider,
				$imagePresenterCallback,
				$config['imageMask']
			))->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('mediaEventSubscriber'))
			->setClass(\Brosland\Media\Models\MediaEventSubscriber::class)
			->setArguments(array ($fileStorage, $imageStorage))
			->addTag(\Kdyby\Events\DI\EventsExtension::TAG_SUBSCRIBER);
	}

	public function beforeCompile()
	{
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();

		$builder->getDefinition('nette.latteFactory')
			->addSetup(\Brosland\Media\Latte\MediaMacros::class . '::install(?->getCompiler())', array ('@self'));

		$builder->getDefinition('brosland.routerFactory')
			->addSetup('addRouter', array ($builder->getDefinition($this->prefix('fileRoute'))))
			->addSetup('addRouter', array ($builder->getDefinition($this->prefix('imageRoute'))));
	}

	/**
	 * @return array
	 */
	public function getEntityMappings()
	{
		return array ('Brosland\Media\Models' => __DIR__ . '/../Models');
	}
}