<?php

namespace Brosland\Media;

interface IImageFormatProvider
{

	/**
	 * @param string $name
	 * @return IImageFormat
	 */
	public function findOneByName($name);

	/**
	 * @return IImageFormat[]
	 */
	public function findAll();
}