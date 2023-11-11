<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

/**
 * Image Resizer.
 *
 * @author    : Harish Chauhan
 * @copyright : Freeware
 *            About :This PHP script will resize the given image and can show on the fly or save as image file.
 *
 */


if(!defined("HAR_AUTO_NAME"))
{
	define("HAR_AUTO_NAME", 1);
}

class JS_Image_Resizer
{
	var $imgFile            = "";
	var $imgWidth           = 0;
	var $imgHeight          = 0;
	var $imgType            = "";
	var $imgAttr            = "";
	var $type               = NULL;
	var $_img               = NULL;
	var $_error             = "";
	var $quality            = 80;
	var $background         = FALSE;
	var $stretch_if_smaller = FALSE;

	var $_border = NULL;

	var $chunkGifs      = TRUE;
	var $transparentGif = FALSE;

	var $tempDir = NULL;

	/**
	 * Constructor
	 *
	 * @param [String $imgFile] Image File Name
	 *
	 * @return RESIZEIMAGE (Class Object)
	 */

	function __construct($imgFile = "", $tempDir = JPATH_CACHE)
	{
		if(!function_exists("imagecreate"))
		{
			$this->_error = "Error: GD Library is not available.";

			return FALSE;
		}

		$this->type = Array(1  => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF',
							8  => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF',
							15 => 'WBMP', 16 => 'XBM'
		);
		if(!empty($imgFile))
		{
			$this->setImage($imgFile);
		}

		$this->tempDir = $tempDir;
	}

	/**
	 * Error occured while resizing the image.
	 *
	 * @return String
	 */
	function error()
	{
		return $this->_error;
	}

	/**
	 * Set image border (look at picture plugin :))
	 *
	 * @param $border string | gd resource Image file
	 *
	 * @return boolean
	 */
	function setBorder($border = NULL)
	{
		// unset border
		if(is_null($border))
		{
			if(is_resource($this->_border))
			{
				@imagedestroy($this->_border);
			}

			return TRUE;
		}

		if(is_resource($border))
		{
			$this->_border = $border;

			return TRUE;
		}

		if(file_exists($border))
		{
			$this->_border = $this->_readImageToGD($border);

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Set image file name
	 *
	 * @param String $imgFile
	 *
	 * @return void
	 */
	function setImage($imgFile)
	{
		$this->imgFile = $imgFile;

		return $this->_createImage();
	}

	function getDate($imgFile)
	{
		$params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
		$parts  = explode('_', $imgFile);
		$date   = date($params->get('folder_format', 'Y-m'), (int)$parts[0]);

		return $date;
	}

	/**
	 *
	 * @return void
	 */
	function close()
	{
		if(is_resource($this->_border))
		{
			@imagedestroy($this->_border);
		}

		return @imagedestroy($this->_img);
	}

	/**
	 * Resize a image to given width and height and keep it's current width and height ratio
	 *
	 * @param Number  $imgwidth
	 * @param Numnber $imgheight
	 * @param String  $newfile
	 */
	function resize_limitwh($imgwidth, $imgheight, $newfile = NULL)
	{
		$image_per = 100;
		list($width, $height, $type, $attr) = @getimagesize($this->imgFile);
		if(($width > $imgwidth || $this->stretch_if_smaller) && $imgwidth > 0)
		{
			$image_per = (double)(($imgwidth * 100) / $width);
		}

		if(floor(($height * $image_per) / 100) > $imgheight && $imgheight > 0)
		{
			$image_per = (double)(($imgheight * 100) / $height);
		}

		$this->resize_percentage($image_per, $newfile);

	}

	function resize_fit($newWidth, $newHeight, $newfile = NULL)
	{
		$image_per = 100;
		list($width, $height, $type, $attr) = @getimagesize($this->imgFile);
		if(($width > $newWidth || $this->stretch_if_smaller) && $newWidth > 0)
		{
			$image_per = (double)(($newWidth * 100) / $width);
		}

		if(floor(($height * $image_per) / 100) > $newHeight && $newHeight > 0)
		{
			$image_per = (double)(($newHeight * 100) / $height);
		}


		$this->resize_percentage($image_per, $newfile);

		if($this->imgType == 'GIF' && $this->chunkGifs)
		{
			return;
		}

		$this->setImage($newfile);

		$thumb = imagecreatetruecolor($newWidth, $newHeight);
		imagealphablending($thumb, FALSE);
		imagesavealpha($thumb, TRUE);

		$rgb   = $this->HexToRGB($this->background);
		$color = imagecolorallocate($thumb, $rgb[0], $rgb[1], $rgb[2]);
		imagefill($thumb, 0, 0, $color);
		imagecopy($thumb, $this->_img, $newWidth / 2 - $this->imgWidth / 2, $newHeight / 2 - $this->imgHeight / 2, 0, 0, $this->imgWidth, $this->imgHeight);

		$this->_storeFile($thumb, $newfile);

		imagedestroy($this->_img);
		imagedestroy($thumb);

	}

	/**
	 * Resize an image to given percentage.
	 *
	 * @param Number $percent
	 * @param String $newfile
	 *
	 * @return Boolean
	 */
	function resize_percentage($percent = 100, $newfile = NULL)
	{
		$newWidth  = ($this->imgWidth * $percent) / 100;
		$newHeight = ($this->imgHeight * $percent) / 100;


		return $this->resize($newWidth, $newHeight, $newfile);
	}

	/**
	 * Resize an image to given X and Y percentage.
	 *
	 * @param Number $xpercent
	 * @param Number $ypercent
	 * @param String $newfile
	 *
	 * @return Boolean
	 */
	function resize_xypercentage($xpercent = 100, $ypercent = 100, $newfile = NULL)
	{
		$newWidth  = ($this->imgWidth * $xpercent) / 100;
		$newHeight = ($this->imgHeight * $ypercent) / 100;

		return $this->resize($newWidth, $newHeight, $newfile);
	}

	/**
	 * Resize an image to given width and height
	 *
	 * @param Number $width
	 * @param Number $height
	 * @param String $newfile
	 *
	 * @return Boolean
	 */
	function resize($width, $height, $newfile = NULL)
	{
		if(empty($this->imgFile))
		{
			$this->_error = "File name is not initialised.";

			return FALSE;
		}
		if($this->imgWidth <= 0 || $this->imgHeight <= 0)
		{
			$this->_error = "Could not resize given image";

			return FALSE;
		}
		if($width <= 0)
		{
			$width = $this->imgWidth;
		}
		if($height <= 0)
		{
			$height = $this->imgHeight;
		}

		return $this->_resize($width, $height, $newfile);
	}

	/**
	 * Get the image attributes
	 *
	 * @access Private
	 *
	 */
	function _getImageInfo()
	{
		@list($this->imgWidth, $this->imgHeight, $type, $this->imgAttr) = @getimagesize($this->imgFile);
		$this->imgType = @$this->type[$type];
	}

	/**
	 * Create the image resource
	 *
	 * @access Private
	 * @return Boolean
	 */
	function _createImage()
	{
		$this->_getImageInfo($this->imgFile);
		$this->_img = $this->_readImageToGD($this->imgFile);


		if(!$this->_img || !@is_resource($this->_img))
		{
			$this->_error = "Error loading " . $this->imgFile;

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Read file into GD resource
	 *
	 * @param $file string Path to file
	 *
	 * @return resource|boolean
	 */
	function _readImageToGD($file)
	{
		@list($width, $height, $type, $attr) = @getimagesize($file);
		$imgType = @$this->type[$type];

		$resource = NULL;

		if($imgType == 'GIF')
		{
			$resource = @imagecreatefromgif($file);
		}
		elseif($imgType == 'JPG')
		{
			$resource = @imagecreatefromjpeg($file);
		}
		elseif($imgType == 'PNG')
		{
			$resource = @imagecreatefrompng($file);

		}

		if(!$resource || !@is_resource($resource))
		{
			$this->_error = "Error loading " . $file;

			return FALSE;
		}

		return $resource;
	}

	/**
	 * Function is used to resize the image
	 *
	 * @access Private
	 *
	 * @param Number $width
	 * @param Number $height
	 * @param String $newfile
	 *
	 * @return Boolean
	 */
	function _resize($width, $height, $newfile = NULL)
	{
		if(!function_exists("imagecreate"))
		{
			$this->_error = "Error: GD Library is not available.";

			return FALSE;
		}

		if($this->imgType == 'GIF' && $this->chunkGifs)
		{
			return $this->_resizeAnimatedGif($width, $height, $newfile);
		}

		$newimg = @imagecreatetruecolor($width, $height);

		if($this->imgType == 'PNG')
		{
			imagealphablending($this->_img, TRUE);
			imagealphablending($newimg, FALSE);
			imagesavealpha($newimg, TRUE);
			$rgb   = $this->HexToRGB($this->background);
			$color = imagecolortransparent($newimg, imagecolorallocatealpha($newimg, $rgb[0], $rgb[1], $rgb[2], 127));
			imagefill($newimg, 0, 0, $color);
		}

		@imagecopyresampled($newimg, $this->_img, 0, 0, 0, 0, $width, $height, $this->imgWidth, $this->imgHeight);

		// take care about border :)
		if(is_resource($this->_border))
		{
			@imagecopyresampled($newimg, $this->_border, 0, 0, 0, 0, $width, $height, imagesx($this->_border), imagesy($this->_border));
		}

		$this->_storeFile($newimg, $newfile);


		@imagedestroy($newimg);
	}

	/**
	 * Resize the animated gif's by splitting it to frames, and resizing each frame.
	 *
	 * @param $width
	 * @param $height
	 * @param $newfile
	 *
	 * @return array
	 */
	function _resizeAnimatedGif($width, $height, $newfile = NULL)
	{
		// jimport('joomla.filesystem.file');

		$libDir = JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'mint' . DIRECTORY_SEPARATOR . 'image_processing';

		require_once $libDir . DIRECTORY_SEPARATOR . 'gifresizer.php';

		$path = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images/joomcck_thumbs' . DIRECTORY_SEPARATOR . 'gif_frames' . DIRECTORY_SEPARATOR;
		if(!is_dir($path))
		{
			$a = '';
			\Joomla\Filesystem\Folder::create($path, 0777);
			\Joomla\Filesystem\File::write($path . DIRECTORY_SEPARATOR . 'index.html', $a);
		}

		$gr           = new GifResizer;    //New Instance Of GIFResizer
		$gr->temp_dir = $path; //Used for extracting GIF Animation Frames
		$gr->resize($this->imgFile, $newfile, $width, $height); //Resizing the animation into a new file.

		/*return;


		require_once $libDir . DIRECTORY_SEPARATOR . 'GIFDecoder.class.php';
		require_once $libDir . DIRECTORY_SEPARATOR . 'GIFEncoder.class.php';

		// Create gif decoder.
		// $gifDecoder = new GIFDecoder(\Joomla\Filesystem\File::read($this->imgFile));
		$gifDecoder = new GIFDecoder(file_get_contents($this->imgFile));

		// Write the frames to disk.
		$files = array();

		foreach($gifDecoder->GIFGetFrames() as $k => $frame)
		{
			// store the current frame to disk.
			// $filename =\Joomla\Filesystem\Path::clean($this->tempDir. DIRECTORY_SEPARATOR .uniqid('resize_', true));

			$img    = imagecreatefromstring($frame);
			$newimg = @imagecreatetruecolor($width, $height);
			if($this->transparentGif)
			{
				$colorcount = imagecolorstotal($img);
				if($colorcount == 0)
				{
					$colorcount = 256;
				}
				imagetruecolortopalette($newimg, TRUE, $colorcount);
				imagepalettecopy($newimg, $img);
				$transparentcolor = imagecolortransparent($img);
				imagefill($newimg, 0, 0, $transparentcolor);
				imagecolortransparent($newimg, $transparentcolor);
			}

			@imagecopyresampled($newimg, $img, 0, 0, 0, 0, $width, $height, $this->imgWidth, $this->imgHeight);

			// take care about border :)
			if(is_resource($this->_border))
			{
				@imagecopyresampled($newimg, $this->_border, 0, 0, 0, 0, $width, $height, imagesx($this->_border), imagesy($this->_border));
			}

			ob_start();
			imagegif($newimg);
			$files [] = ob_get_clean();

			// free some memory
			@imagedestroy($newimg);
			@imagedestroy($img);

			unset($frame);
			unset($gifDecoder->GIF_arrays[$k]);
		}

		// Create encoder
		$gifEncoder = new GIFEncoder(
			$files,
			$gifDecoder->GIFGetDelays(),
			$gifDecoder->GIFGetLoop(),
			$gifDecoder->GIFGetDisposal(),
			$gifDecoder->GIFGetTransparentR(),
			$gifDecoder->GIFGetTransparentG(),
			$gifDecoder->GIFGetTransparentB(),
			'bin'// 'url'
		);

		unset($gifDecoder);

		foreach($files as $f)
		{
			@unlink($f);
		}

		if(!empty($newfile))
		{
			//\Joomla\Filesystem\File::write($newfile, $gifEncoder->getAnimation());
			file_put_contents($newfile, $gifEncoder->getAnimation());
		}
		else
		{
			@header("Content-type: image/gif");
			echo $gifEncoder->getAnimation();
		}*/
	}

	/**
	 *
	 * @param $newimg  GD resource
	 * @param $newfile string filename
	 *
	 * @return unknown_type
	 */
	function _storeFile($newimg, $newfile)
	{
		//jimport ('joomla.filesystem.file');

		//$newfile =\Joomla\Filesystem\Path::check($newfile);
		if(!empty($newfile))
		{
			$tmpname = JPATH_ROOT . '/tmp/' . md5($newfile);
			switch($this->imgType)
			{
				case 'GIF':
					@imagegif($newimg, $tmpname);
					break;
				case 'JPG':
					@imagejpeg($newimg, $tmpname, $this->quality);
					break;
				case 'PNG':
					@imagepng($newimg, $tmpname);
					break;
			}
			\Joomla\Filesystem\File::move($tmpname, $newfile);
		}
		else
		{
			switch($this->imgType)
			{
				case 'GIF':
					@header("Content-type: image/gif");
					@imagegif($newimg);
					break;
				case 'JPG':
					@header("Content-type: image/jpeg");
					@imagejpeg($newimg);
					break;
				case 'PNG':
					@header("Content-type: image/png");
					@imagepng($newimg);
					break;
			}
		}
	}

	function resizeByHeight($height, $newfile = NULL)
	{
		$ratio     = $this->imgWidth / $this->imgHeight;
		$newWidth  = $height * $ratio;
		$newHeight = $height;
		$this->resize($newWidth, $newHeight, $newfile);

		return $newWidth;
	}

	function resizeByWidth($width, $newfile = NULL)
	{
		$ratio     = $this->imgHeight / $this->imgWidth;
		$newHeight = $width * $ratio;
		$newWidth  = $width;

		return $this->resize($newWidth, $newHeight, $newfile);
	}

	function calculateByHeight($base_height, $height, $width)
	{
		$ratio = $width / $height;

		return $base_height * $ratio;
	}

	function calculateHeightProportionalByWidth($sum_width, $base_height, $column_width, $image_count, $padding = 0)
	{
		$column_width -= ($image_count * ($padding * 2));
		$ratio = ($column_width / $sum_width);

		return $base_height * $ratio;
	}

	function resize_crop($thumbnail_width, $thumbnail_height, $newfile = NULL)
	{
		imagealphablending($this->_img, TRUE);

		$ratio_orig = $this->imgWidth / $this->imgHeight;

		if($thumbnail_width / $thumbnail_height > $ratio_orig)
		{
			$new_height = $thumbnail_width / $ratio_orig;
			$new_width  = $thumbnail_width;
		}
		else
		{
			$new_width  = $thumbnail_height * $ratio_orig;
			$new_height = $thumbnail_height;
		}

		if($this->imgType == 'GIF' && $this->chunkGifs)
		{
			return $this->_resizeAnimatedGif($new_width, $new_height, $newfile);
		}

		$x_mid = $new_width / 2; //horizontal middle
		$y_mid = $new_height / 2; //vertical middle


		$smaller = FALSE;

		if($this->imgWidth < $thumbnail_width && $this->imgHeight < $thumbnail_height && !$this->stretch_if_smaller)
		{
			$smaller = TRUE;
		}
		$thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		imagealphablending($thumb, FALSE);
		imagesavealpha($thumb, TRUE);

		if(!$smaller)
		{
			$process = imagecreatetruecolor(round($new_width), round($new_height));
			imagealphablending($process, FALSE);
			imagesavealpha($process, TRUE);

			imagecopyresampled($process, $this->_img, 0, 0, 0, 0, $new_width, $new_height, $this->imgWidth, $this->imgHeight);
			imagecopyresampled($thumb, $process, 0, 0, ($x_mid - ($thumbnail_width / 2)), ($y_mid - ($thumbnail_height / 2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);
			imagedestroy($process);
		}
		else
		{
			$rgb   = $this->HexToRGB($this->background);
			$color = imagecolorallocate($thumb, $rgb[0], $rgb[1], $rgb[2]);
			imagefill($thumb, 0, 0, $color);
			imagecopy($thumb, $this->_img, $thumbnail_width / 2 - $this->imgWidth / 2, $thumbnail_height / 2 - $this->imgHeight / 2, 0, 0, $this->imgWidth, $this->imgHeight);
		}

		$this->_storeFile($thumb, $newfile);

		imagedestroy($this->_img);
		imagedestroy($thumb);
	}

	function resize_crop_top($thumbnail_width, $thumbnail_height, $newfile = NULL)
	{
		imagealphablending($this->_img, TRUE);

		$ratio_orig = $this->imgWidth / $this->imgHeight;

		if($thumbnail_width / $thumbnail_height > $ratio_orig)
		{
			$new_height = $thumbnail_width / $ratio_orig;
			$new_width  = $thumbnail_width;
		}
		else
		{
			$new_width  = $thumbnail_height * $ratio_orig;
			$new_height = $thumbnail_height;
		}

		$x_mid = $new_width / 2; //horizontal middle
		$y_mid = $new_height / 2; //vertical middle


		$smaller = FALSE;

		if($this->imgWidth < $thumbnail_width && $this->imgHeight < $thumbnail_height && !$this->stretch_if_smaller)
		{
			$smaller = TRUE;
		}
		$thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		imagealphablending($thumb, FALSE);
		imagesavealpha($thumb, TRUE);

		if(!$smaller)
		{
			$process = imagecreatetruecolor(round($new_width), round($new_height));
			imagealphablending($process, FALSE);
			imagesavealpha($process, TRUE);

			imagecopyresampled($process, $this->_img, 0, 0, 0, 0, $new_width, $new_height, $this->imgWidth, $this->imgHeight);
			imagecopyresampled($thumb, $process, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);
			imagedestroy($process);
		}
		else
		{
			$rgb   = $this->HexToRGB($this->background);
			$color = imagecolorallocate($thumb, $rgb[0], $rgb[1], $rgb[2]);
			imagefill($thumb, 0, 0, $color);
			imagecopy($thumb, $this->_img, $thumbnail_width / 2 - $this->imgWidth / 2, $thumbnail_height / 2 - $this->imgHeight / 2, 0, 0, $this->imgWidth, $this->imgHeight);
		}

		$this->_storeFile($thumb, $newfile);

		imagedestroy($this->_img);
		imagedestroy($thumb);
	}

	function setCropSize($width, $height)
	{
		$this->imgWidth  = $width;
		$this->imgHeight = $height;
	}

	/*    function cropedThumb($newWidth, $newHeight, $newfile=NULL)
		{
			if ($newHeight < $newWidth) {
				$optimalWidth = $this->imgWidth / $this->imgHeight * $newHeight;
				$optimalHeight= $newHeight;
			} else if ($newHeight > $newWidth) {
				$optimalWidth = $newWidth;
				$optimalHeight= $this->imgHeight / $this->imgWidth * $newWidth;
			} else {
				// *** Sqaure being resized to a square
				$optimalWidth = $newWidth;
				$optimalHeight= $newHeight;
			}
			// *** Find center - this will be used for the crop
			$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
			$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );

			$this->_resize($optimalWidth, $optimalHeight, $newfile);

			$crop = $this->setImage($newfile);
			// *** Now crop from center to exact requested size
			$newimg = imagecreatetruecolor($newWidth , $newHeight);
			imagealphablending($this->_img, true);
			imagealphablending($newimg, false);
			imagesavealpha($newimg, true);
			$rgb = $this->HexToRGB($this->background);
			$color = imagecolortransparent($newimg, imagecolorallocatealpha($newimg, $rgb[0], $rgb[1], $rgb[2], 127));
			imagefill($newimg, 0, 0, $color);
			imagecopyresampled($newimg, $this->_img , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);

			$this->_storeFile($newimg, $newfile);


			@imagedestroy($newimg);
		}*/

	function HexToRGB()
	{
		$hex = $this->background;
		if(!$hex)
		{
			return array(0, 0, 0);
		}
		$hex   = str_replace("#", "", $hex);
		$color = array();

		if(strlen($hex) == 3)
		{
			$color[] = hexdec(substr($hex, 0, 1));
			$color[] = hexdec(substr($hex, 1, 1));
			$color[] = hexdec(substr($hex, 2, 1));
		}
		else if(strlen($hex) == 6)
		{
			$color[] = hexdec(substr($hex, 0, 2));
			$color[] = hexdec(substr($hex, 2, 2));
			$color[] = hexdec(substr($hex, 4, 2));
		}

		return $color;
	}

}