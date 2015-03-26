<?php

namespace Brosland\Media;

interface IFile
{

	/**
	 * @return string
	 */
	public function getFullname();

	/**
	 * @return string
	 */
	public function getLabel();

	/**
	 * @return string
	 */
	public function getContentType();

	/**
	 * @return string
	 */
	public function getExt();

	/**
	 * @return \DateTime
	 */
	public function getUploaded();
}