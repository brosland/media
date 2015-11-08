<?php

namespace Brosland\Media\DI;

class MediaExtension extends \Nette\DI\CompilerExtension
{

	/**
	 * @var array
	 */
	private static $DEFAULTS = [
		'fileStorageDir' => '%appDir%/../storage',
		'imageStorageDir' => '%appDir%/../storage',
		'fileRouteMask' => 'assets/<file>',
		'imagePath' => '%wwwDir%/images/<format>/<month>/<image>'
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig(self::$DEFAULTS);

		if (!$builder->hasDefinition($this->prefix('imageFormatProvider')))
		{
			$builder->addDefinition($this->prefix('imageFormatProvider'))
				->setClass(\Brosland\Media\Model\ImageFormatProvider::class);
		}

		$imageFormatProvider = $builder->getDefinition($this->prefix('imageFormatProvider'));

		if (!$builder->hasDefinition($this->prefix('fileStorage')))
		{
			$builder->addDefinition($this->prefix('fileStorage'))
				->setClass(\Brosland\Media\Storages\FileStorage::class)
				->setArguments([$config['fileStorageDir']]);
		}

		$fileStorage = $builder->getDefinition($this->prefix('fileStorage'));

		if (!$builder->hasDefinition($this->prefix('imageStorage')))
		{
			$builder->addDefinition($this->prefix('imageStorage'))
				->setClass(\Brosland\Media\Storages\ImageStorage::class)
				->setArguments([
					$config['imageStorageDir'],
					$config['imagePath'],
					$imageFormatProvider
			]);
		}

		$imageStorage = $builder->getDefinition($this->prefix('imageStorage'));

		if (!$builder->hasDefinition($this->prefix('filePresenterCallback')))
		{
			$builder->addDefinition($this->prefix('filePresenterCallback'))
				->setClass(\Brosland\Media\Callbacks\FilePresenterCallback::class)
				->setArguments([
					'@' . \Brosland\Media\IFileProvider::class,
					$fileStorage
				])
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('imagePresenterCallback')))
		{
			$builder->addDefinition($this->prefix('imagePresenterCallback'))
				->setClass(\Brosland\Media\Callbacks\ImagePresenterCallback::class)
				->setArguments([
					'@' . \Brosland\Media\IImageProvider::class,
					$imageFormatProvider,
					$imageStorage
				])
				->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('fileRouter')))
		{
			$builder->addDefinition($this->prefix('fileRouter'))
				->setFactory(\Brosland\Media\Routers\FileRouterFactory::class . '::createRouter')
				->setArguments([
					$config['fileRouteMask'],
					$builder->getDefinition($this->prefix('filePresenterCallback'))
				])->setAutowired(FALSE);
		}

		if (!$builder->hasDefinition($this->prefix('imageRouter')))
		{
			$imageRoute = substr($config['imagePath'], strlen($builder->parameters['wwwDir']) + 1);

			$builder->addDefinition($this->prefix('imageRouter'))
				->setFactory(\Brosland\Media\Routers\ImageRouterFactory::class . '::createRouter')
				->setArguments([
					$imageRoute,
					$builder->getDefinition($this->prefix('imagePresenterCallback'))
				])->setAutowired(FALSE);
		}
	}

	public function beforeCompile()
	{
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();

		$builder->getDefinition('nette.latteFactory')
			->addSetup(\Brosland\Media\Latte\MediaMacros::class . '::install(?->getCompiler())', ['@self']);
	}
}