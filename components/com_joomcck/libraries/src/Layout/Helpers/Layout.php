<?php
/**
* Joomcck by joomcoder
* a component for Joomla 4 CMS (http://www.joomla.org)
* Author Website: https://www.joomcoder.com/
* @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
* @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

namespace Joomcck\Layout\Helpers;


use ItemsStore;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die();

Class Layout extends LayoutHelper {



	public static function render($layoutFile, $displayData = null, $basePath = '', $options = null)
	{


		// if null options make sure joomcck frontend used
		if(is_null($options)){

			$options = [
				'site' => 'client',
				'component' => 'com_joomcck'
			];

		}

		// override core layouts by apps
		if(str_contains($layoutFile, 'core.')){
			// get layout path in section folder
			$sectionLayoutFile = self::getSectionLayoutFile($layoutFile);

			// render layout if found in layout/apps/sectionName
			$display = parent::render($sectionLayoutFile, $displayData, $basePath, $options);

			// if not empty return it
			if(!empty($display))
				return $display;
		}

		// if not return core layout
		return parent::render($layoutFile, $displayData, $basePath, $options);

	}


	public static function getSectionLayoutFile($layoutFile){

		// get section
		$section = ItemsStore::getSection(Factory::getApplication()->input->getInt('section_id'));

		// get section layout folder name
		$sectionLayoutFolder = self::kebabToCamelCase($section->alias);


		if(str_contains($layoutFile, "core.apps.$sectionLayoutFolder."))
			$layoutFile = str_replace("core.apps.$sectionLayoutFolder.","apps.$sectionLayoutFolder.",$layoutFile);
		else
			$layoutFile = str_replace('core.',"apps.$sectionLayoutFolder.",$layoutFile);

		return $layoutFile;
	}

	public static function kebabToCamelCase($string) {
		return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', (string) $string))));
	}



}