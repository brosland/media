<?php

namespace Brosland\Media;

interface IFileProvider
{

	/**
	 * @param string $name
	 * @return IFile
	 */
	public function findOneByName($name);
}