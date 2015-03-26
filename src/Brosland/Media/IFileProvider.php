<?php

namespace Brosland\Media;

interface IFileProvider
{

	/**
	 * @param string $fullname
	 * @return IFile
	 */
	public function findOneByFullname($fullname);
}