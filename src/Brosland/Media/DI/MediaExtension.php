<?php

namespace Brosland\Media\DI;

use Brosland\Media\Model\FileEntity,
	Brosland\Media\Model\ImageEntity,
	Kdyby\Doctrine\DI\IEntityProvider,
	Nette\DI\Statement;

class MediaExtension extends \Nette\DI\CompilerExtension implements IEntityProvider
{

	/**
	 * @var array
	 */
	private static $DEFAULTS = [
		'fileStorageDir' => '%appDir%/../storage/files',
		'imageStorageDir' => '%appDir%/../storage/images',
		'imagePath' => '%wwwDir%/images',
		'fileRoute' => 'assets/<month>/<file>',
		'imageRoute' => 'images/<format>/<month>/<image>',
		'fileMask' => '<file>',
		'imageMask' => '<image>'
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig(self::$DEFAULTS);

		if (!$builder->hasDefinition($this->prefix('fileProvider')))
		{
			$builder->addDefinition($this->prefix('fileProvider'))
				->setClass(\Brosland\Media\Model\FileProvider::class)
				->setArguments([new Statement('@doctrine.dao', [FileEntity::class])]);
		}

		$fileProvider = $builder->getDefinition($this->prefix('fileProvider'));

		if (!$builder->hasDefinition($this->prefix('imageProvider')))
		{
			$builder->addDefinition($this->prefix('imageProvider'))
				->setClass(\Brosland\Media\Model\FileProvider::class)
				->setArguments([new Statement('@doctrine.dao', [ImageEntity::class])]);
		}

		$imageProvider = $builder->getDefinition($this->prefix('imageProvider'));

		if (!$builder->hasDefinition($this->prefix('imageFormatProvider')))
		{
			$builder->addDefinition($this->prefix('imageFormatProvider'))
				->setClass(\Brosland\Media\Model\ImageFormatProvider::class);
		}

		$imageFormatProvider = $builder->getDefinition($this->prefix('imageFormatProvider'));

		$fileStorage = $builder->addDefinition($this->prefix('fileStorage'))
			->setClass(\Brosland\Media\Storages\FileStorage::class)
			->setArguments([$builder->expand($config['fileStorageDir'])]);

		$imageStorage = $builder->addDefinition($this->prefix('imageStorage'))
			->setClass(\Brosland\Media\Storages\ImageStorage::class)
			->setArguments([
			$builder->expand($config['imageStorageDir']),
			$builder->expand($config['imagePath']),
			$imageFormatProvider
		]);

		$filePresenterCallback = $builder->addDefinition($this->prefix('filePresenterCallback'))
			->setClass(\Brosland\Media\Callbacks\FilePresenterCallback::class)
			->setArguments([$fileStorage])
			->setAutowired(FALSE);

		$imagePresenterCallback = $builder->addDefinition($this->prefix('imagePresenterCallback'))
			->setClass(\Brosland\Media\Callbacks\ImagePresenterCallback::class)
			->setArguments([$imageStorage])
			->setAutowired(FALSE);

		$fileRouter = $builder->addDefinition($this->prefix('fileRouter'))
				->setClass(\Brosland\Media\Routers\FileRouter::class)
				->setArguments([
					$config['fileRoute'],
					$fileProvider,
					$filePresenterCallback,
					$config['fileMask']
				])->setAutowired(FALSE);

		$imageRouter = $builder->addDefinition($this->prefix('imageRouter'))
				->setClass(\Brosland\Media\Routers\ImageRouter::class)
				->setArguments([
					$config['imageRoute'],
					$imageProvider,
					$imageFormatProvider,
					$imagePresenterCallback,
					$config['imageMask']
				])->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('mediaSubscriber'))
			->setClass(\Brosland\Media\Model\MediaSubscriber::class)
			->setArguments([$fileStorage, $imageStorage])
			->addTag(\Kdyby\Events\DI\EventsExtension::TAG_SUBSCRIBER);

		if ($builder->hasDefinition('router'))
		{
			$builder->getDefinition('router')
				->addSetup('offsetSet', [NULL, $fileRouter])
				->addSetup('offsetSet', [NULL, $imageRouter]);
		}
	}

	public function beforeCompile()
	{
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();

		$builder->getDefinition('nette.latteFactory')
			->addSetup(\Brosland\Media\Latte\MediaMacros::class . '::install(?->getCompiler())', ['@self']);
	}

	/**
	 * @return array
	 */
	public function getEntityMappings()
	{
		return ['Brosland\Media\Model' => __DIR__ . '/../Model'];
	}
}