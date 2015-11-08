<?php

namespace Brosland\Media;

interface IImageProvider
{

	/**
	 * @param string $name
	 * @return IFile
	 */
	public function findOneByName($name);
}