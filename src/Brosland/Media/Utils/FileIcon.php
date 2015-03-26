<?php

namespace Brosland\Media\Utils;

class FileIcon extends \Nette\Object
{
	/**
	 * @var array
	 */
	public static $ICONS = array (
		'fa fa-file-archive-o' => array ('zip', 'rar', 'tar', '7z'),
		'fa fa-file-audio-o' => array ('wav', 'mp3', 'ogg', 'flac'),
		'fa fa-file-video-o' => array ('avi', 'mpg', '3gp', 'mp4', 'flv'),
		'fa fa-file-image-o' => array ('bmp', 'jpg', 'png', 'gif'),
		'fa fa-file-code-o' => array ('css', 'js', 'php', 'java', 'cpp', 'c',
			'sql', 'xml', 'latte', 'html'),
		'fa fa-file-text-o' => array ('txt', 'neon', 'ini'),
		'fa fa-file-word-o' => array ('doc', 'docx', 'rtf', 'odt'),
		'fa fa-file-excel-o' => array ('xls', 'xlt', 'xlsx'),
		'fa fa-file-powerpoint-o' => array ('ppt', 'pptx', 'odp'),
		'fa fa-file-pdf-o' => array ('pdf')
	);
	/**
	 * @var string
	 */
	public static $DEFAULT_ICON = 'fa fa-file-o';


	/**
	 * @param string $filePath
	 * @return string
	 */
	public static final function getIcon($filePath)
	{
		$ext = \Nette\Utils\Strings::lower(pathinfo($filePath, PATHINFO_EXTENSION));

		foreach (self::$ICONS as $icon => $extensions)
		{
			if (in_array($ext, $extensions))
			{
				return $icon;
			}
		}

		return self::$DEFAULT_ICON;
	}
}