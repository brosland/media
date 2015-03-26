<?php

namespace Brosland\Media;

interface IFileCallback
{

	/**
	 * @param IFile $file
	 */
	public function __invoke(IFile $file);
}