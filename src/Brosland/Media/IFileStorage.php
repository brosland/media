<?php

namespace Brosland\Media;

interface IFileStorage
{

	/**
	 * @param IFile $file
	 * @return string path to file in storage
	 */
	public function getPath(IFile $file);

	/**
	 * @param string $pathToUploadedFile
	 * @param IFile $file
	 * @return string path to file in storage
	 */
	public function save($pathToUploadedFile, IFile $file);

	/**
	 * @param IFile $file
	 */
	public function remove(IFile $file);
}