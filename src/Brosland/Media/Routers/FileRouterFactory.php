<?php

namespace Brosland\Media\Routers;

use Brosland\Media\IFileCallback,
	Nette\Application\Routers\Route;

class FileRouterFactory extends \Nette\Object
{

	/**
	 * @param string $mask example assets/<month>/<file>
	 * @param IFileCallback $presenterCallback
	 * @return Route
	 */
	public static function createRouter($mask, IFileCallback $presenterCallback)
	{
		$filterIn = function ($params)
		{
			if ($params['presenter'] != 'Nette:Micro' || !isset($params['file']))
			{
				return NULL;
			}

			return $params;
		};
		$filterOut = function ($params) use ($mask)
		{
			if ($params['presenter'] != 'Nette:Micro' || !isset($params['file']))
			{
				return NULL;
			}

			if ($params['file'] instanceof \Brosland\Media\IFile)
			{
				$file = $params['file'];
				/* @var $file \Brosland\Media\IFile */

				$params['file'] = $params['file']->getName();

				if (preg_match('/<month>/', $mask))
				{
					$params['month'] = $file->getUploaded()->format('Ym');
				}
			}

			return $params;
		};
		$callback = function ($file) use ($presenterCallback)
		{
			return \Nette\Utils\Callback::invokeArgs($presenterCallback, [$file]);
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