<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Gumlet\ImageResize;

defined('_JEXEC') or die();

jimport('mint.resizeimage');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class CImgHelper {

	const RESIZE_CROP			= 1;
	const RESIZE_FIT 			= 2;
	const RESIZE_FULL			= 3;
	const RESIZE_PROPORTIONAL 	= 6;
	const RESIZE_WIDTHBASED		= 4;
	const RESIZE_HEIGHTBASED	= 5;

	static $img = null;

	static public function getThumb($file, $width, $height, $folder, $user_id = 0, $options = array())
	{
		static $resizer = null;

		if(!JFile::exists($file))
		{
			return;
		}



		$key = md5($file.$width.$height.implode('-', $options));
		$options = new JRegistry($options);
		$index = '';
		$ext = strtolower(JFile::getExt($file));

		$path = JPATH_ROOT. DIRECTORY_SEPARATOR .'images/joomcck_thumbs'. DIRECTORY_SEPARATOR .$folder.DIRECTORY_SEPARATOR;


        if(!JFolder::exists($path))
        {
        	JFolder::create($path, 0755);
        	JFile::write($path. DIRECTORY_SEPARATOR .'index.html', $index);
        }

		$path .= (int)$user_id.DIRECTORY_SEPARATOR;

        if(!JFolder::exists($path))
        {
        	JFolder::create($path, 0755);
        	JFile::write($path. DIRECTORY_SEPARATOR .'index.html', $index);
        }

        $img = $path.$key.'.'.$ext;

		if(!JFile::exists($img))
		{

			$resizer = new ImageResize($file);

			//$resizer->stretch_if_smaller = $options->get('strache', 1);
			$resizer->quality_jpg = $options->get('quality', 90);
			//$resizer->background = $options->get('background', '#000000');


			switch ($options->get('mode', self::RESIZE_PROPORTIONAL))
			{


				case 1 :
					$resizer->crop($width, $height);
					$resizer->save($img);
					break;
				case 2 :
					$resizer->resizeToBestFit($width, $height);
					$resizer->save($img);
					break;
				case 3 :
					$resizer->resize($width, $height);
					$resizer->save($img);
					break;
				case 4 :
					$resizer->resizeToWidth($width);
					$resizer->save($img);
					break;
				case 5 :
					$resizer->resizeToHeight($height, $img);
					$resizer->save($img);
					break;
				case 7 :
					$resizer->crop($width, $height, true,ImageResize::CROPTOP);
					$resizer->save($img);
					break;
				case 6 :
				default:
					$resizer->resizeToLongSide($width, $height, true);
					$resizer->save($img);
					break;
			}
		}

		self::$img = $img;

		return JURI::root(TRUE).'/images/joomcck_thumbs/'.$folder.'/'.(int)$user_id.'/'.$key.'.'.$ext;
	}

	/**
	 * 	Return result of function 'getimagesize' of last created thumb or empty array if image not exist
	 *
	 * @return array
	 */

	static public function getThumbSize()
	{
		if(isset(self::$img))
		{
			return getimagesize(self::$img);
		}

		return array();
	}
}