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
use Joomcck\Ui\Helpers\UiSystemHelper;

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

		// Check if Modern UI is enabled
		$isModern = UiSystemHelper::isModern();

		// override core layouts by apps (with modern support)
		if(str_contains($layoutFile, 'core.')){

			// 1. Try section-specific modern layout first (if modern enabled)
			if ($isModern) {
				$sectionModernLayout = self::getSectionModernLayoutFile($layoutFile);
				$display = parent::render($sectionModernLayout, $displayData, $basePath, $options);
				if(!empty($display)) {
					return $display;
				}
			}

			// 2. Try section-specific legacy layout
			$sectionLayoutFile = self::getSectionLayoutFile($layoutFile);
			$display = parent::render($sectionLayoutFile, $displayData, $basePath, $options);
			if(!empty($display)) {
				return $display;
			}

			// 3. Try core modern layout (if modern enabled)
			if ($isModern) {
				$modernLayout = str_replace('core.', 'modern.', $layoutFile);
				$display = parent::render($modernLayout, $displayData, $basePath, $options);
				if(!empty($display)) {
					return $display;
				}
			}
		}

		// 4. Fall back to original core layout
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

	/**
	 * Get section-specific modern layout file path
	 * Priority: apps/{sectionName}/modern/{path}
	 *
	 * @param string $layoutFile Original layout file path (e.g., core.submission.entry)
	 * @return string Modified layout file path for section-specific modern layout
	 */
	public static function getSectionModernLayoutFile($layoutFile){

		// get section
		$section = ItemsStore::getSection(Factory::getApplication()->input->getInt('section_id'));

		// get section layout folder name
		$sectionLayoutFolder = self::kebabToCamelCase($section->alias);

		// apps/{sectionName}/modern/{path}
		// e.g., core.submission.entry -> apps.knowledgeBase.modern.submission.entry
		return str_replace('core.', "apps.$sectionLayoutFolder.modern.", $layoutFile);
	}

	public static function kebabToCamelCase($string) {
		return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', (string) $string))));
	}



}