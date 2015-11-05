<?php

namespace Brosland\Media\Routers;

use Brosland\Media\IFileCallback,
	Brosland\Media\IFileProvider,
	Nette\Http\Url,
	Nette\Application\Routers\Route,
	Nette\Http\IRequest,
	Nette\Application\Request;

class FileRouter extends \Nette\Object implements \Nette\Application\IRouter
{

	/**
	 * @var Route
	 */
	private $route;


	/**
	 * @param string $mask example '/some/<file>'
	 * @param IFileProvider $fileProvider
	 * @param IFileCallback $callback
	 * @param string $nameMask example '<file>'
	 */
	public function __construct($mask, IFileProvider $fileProvider,
		IFileCallback $callback, $nameMask = '<file>')
	{
		$this->route = new Route($mask, function ($file)
			use ($fileProvider, $callback, $nameMask)
		{
			$name = str_replace(['<file>'], [$file], $nameMask);
			$fileEntity = $fileProvider->findOneByName($name);

			if (!$fileEntity)
			{
				throw new \Nette\Application\BadRequestException('File not found', 404);
			}

			return \Nette\Utils\Callback::invokeArgs($callback, [$fileEntity]);
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
	 * @param Url $refUrl referential URI
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