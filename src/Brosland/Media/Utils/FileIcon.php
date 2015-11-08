<?php

namespace Brosland\Media\Utils;

class FileIcon extends \Nette\Object
{

	/**
	 * @var array
	 */
	public static $ICONS = [
		'fa fa-file-archive-o' => ['zip', 'rar', 'tar', '7z'],
		'fa fa-file-audio-o' => ['wav', 'mp3', 'ogg', 'flac'],
		'fa fa-file-video-o' => ['avi', 'mpg', '3gp', 'mp4', 'flv'],
		'fa fa-file-image-o' => ['bmp', 'jpg', 'png', 'gif'],
		'fa fa-file-code-o' => [
			'css', 'js', 'php', 'java', 'cpp', 'c', 'sql', 'xml', 'latte', 'html'
		],
		'fa fa-file-text-o' => ['txt', 'neon', 'ini'],
		'fa fa-file-word-o' => ['doc', 'docx', 'rtf', 'odt'],
		'fa fa-file-excel-o' => ['xls', 'xlt', 'xlsx'],
		'fa fa-file-powerpoint-o' => ['ppt', 'pptx', 'odp'],
		'fa fa-file-pdf-o' => ['pdf']
	];
	/**
	 * @var string
	 */
	public static $DEFAULT_ICON = 'fa fa-file-o';


	/**
	 * @param string $ext
	 * @return string
	 */
	public static final function getIcon($ext)
	{
		$ext = \Nette\Utils\Strings::lower($ext);

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