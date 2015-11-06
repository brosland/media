<?php

namespace Brosland\Media\Routers;

use Brosland\Media\IFile,
	Brosland\Media\IImageCallback,
	Brosland\Media\IImageFormat,
	Nette\Application\Routers\Route;

class ImageRouterFactory extends \Nette\Object
{

	/**
	 * @param string $mask example images/<format>/<month>/<image>
	 * @param IImageCallback $presenterCallback
	 * @return Route
	 */
	public static function createRouter($mask, IImageCallback $presenterCallback)
	{
		$filterIn = function ($params)
		{
			if ($params['presenter'] != 'Nette:Micro' || !isset($params['image']) || !isset($params['format']))
			{
				return NULL;
			}

			return $params;
		};
		$filterOut = function ($params) use ($mask)
		{
			if ($params['presenter'] != 'Nette:Micro' || !isset($params['image']) || !isset($params['format']))
			{
				return NULL;
			}

			if ($params['image'] instanceof IFile)
			{
				$image = $params['image'];
				/* @var $file IFile */

				$params['image'] = $image->getName();

				if (preg_match('/<month>/', $mask))
				{
					$params['month'] = $image->getUploaded()->format('Ym');
				}
			}

			if ($params['format'] instanceof IImageFormat)
			{
				$params['format'] = $params['format']->getName();
			}

			return $params;
		};
		$callback = function ($image, $format) use ($presenterCallback)
		{
			return \Nette\Utils\Callback::invokeArgs($presenterCallback, [$image, $format]);
		};

		$route = new Route($mask, [
			Route::PRESENTER_KEY => 'Nette:Micro',
			'callback' => $callback,
			NULL => [
				Route::FILTER_IN => $filterIn,
				Route::FILTER_OUT => $filterOut
			]
		]);

		return $route;
	}
}