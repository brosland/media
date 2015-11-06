<?php

namespace Brosland\Media;

interface IFileCallback
{

	/**
	 * @param string $fileName
	 */
	public function __invoke($fileName);
}