<?php

namespace Brosland\Media\Routers;

use Brosland\Media\IFileProvider,
	Brosland\Media\IImageFormatProvider,
	Brosland\Media\IImageCallback,
	Nette\Http\Url,
	Nette\Application\Routers\Route,
	Nette\Http\IRequest,
	Nette\Application\Request;

class ImageRouter extends \Nette\Object implements \Nette\Application\IRouter
{
	/**
	 * @var \Nette\Application\Routers\Route
	 */
	private $route;


	/**
	 * @param string $mask example '/images/<format>/<image>'
	 * @param IFileProvider $imageProvider
	 * @param IImageFormatProvider $imageFormatProvider
	 * @param IImageCallback $callback
	 * @param string example '<image>'
	 */
	public function __construct($mask, IFileProvider $imageProvider, IImageFormatProvider $imageFormatProvider, IImageCallback $callback, $imageMask = '<image>')
	{
		$this->route = new Route($mask, function ($image, $format)
			use ($imageProvider, $imageFormatProvider, $callback, $imageMask)
		{
			$imageFormatEntity = $imageFormatProvider->findOneByName($format);

			if (!$imageFormatEntity)
			{
				throw new \Nette\Application\BadRequestException('Image format not found.', 404);
			}

			$fullname = str_replace(array ('<image>'), array ($image), $imageMask);
			$imageEntity = $imageProvider->findOneByFullname($fullname);

			if (!$imageEntity)
			{
				throw new \Nette\Application\BadRequestException('Image not found.', 404);
			}

			return \Nette\Utils\Callback::invokeArgs($callback, array ($imageEntity, $imageFormatEntity));
		});
	}

	/**
	 * Maps HTTP request to a PresenterRequest object.
	 *
	 * @param IRequest $httpRequest
	 * @return Request|NULL
	 * @throws \Nette\InvalidStateException
	 */
	public function match(IRequest $httpRequest)
	{
		return $this->route->match($httpRequest);
	}

	/**
	 * Constructs absolute URL from Request object.
	 *
	 * @param Request $appRequest
	 * @param Url referential URI $refUrl
	 * @return string|NULL
	 */
	public function constructUrl(Request $appRequest, Url $refUrl)
	{
		$url = $this->route->constructUrl($appRequest, $refUrl);

		if ($url !== NULL)
		{
			if (is_string($url))
			{
				$url = new Url($url);
			}

			$url->setQuery('')->canonicalize();
		}

		return $url;
	}
}