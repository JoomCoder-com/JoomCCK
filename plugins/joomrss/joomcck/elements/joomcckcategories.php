<?php
/*
 * @version		$Id: joomcckcategories.php 1 2013-07-30 09:25:32Z thongta $
 * @author		Phong Lo - joomboost.com
 * @copyright	Copyright (C) 2007-2011 joomboost.com. All rights reserved.
 * @package		obRSS for Joomla
 * @subpackage	intern addon joomcck
 * @license		GNU/GPL, see LICENSE
 */

// Check to ensure this file is within the rest of the framework
use Joomla\CMS\HTML\HTMLHelper;

defined( 'JPATH_BASE' ) or die();
JFormHelper::loadFieldClass( 'list' );

class JFormFieldjoomcckCategories extends \Joomla\CMS\Form\Field\ListField {
	public $_name = 'joomcckCategories';
	public function getInput()  // New method name in J4
	{
		$options = $this->getOptions();
		$size = (count(explode("\n", $options)) < 10) ? count(explode("\n", $options)) : 10;
		return '<select name="' . $this->name . '" id="' . $this->id . '" class="form-select" multiple="true" size="' . $size . '">' .
			$options .
			'</select>';
	}

	protected function getOptions()
	{
		$sections = $this->getSections();
		$items = [];

		if ($sections) {
			foreach ($sections as $section) {
				$items[] = '<optgroup label="' . htmlspecialchars($section->title) . '">';

				$categories = $this->getCategories($section->id);
				foreach ($categories as $category) {
					$items[] = '<option value="' . $category->id . '">' .
						htmlspecialchars($category->title) . '</option>';
				}

				$items[] = '</optgroup>';
			}
		}

		return implode("\n", $items);
	}

	function getSections() {
		$db  = \Joomla\CMS\Factory::getDbo();
		$sql = 'SELECT * FROM `#__js_res_sections` ORDER BY `id` ASC';
		$db->setQuery( $sql );
		$sections = $db->loadObjectList();

		return $sections;
	}

	function getCategories( $section_id = null ) {
		$db  = \Joomla\CMS\Factory::getDbo();
		$sql = 'SELECT * FROM `#__js_res_categories` WHERE section_id=' . $section_id;
		$db->setQuery( $sql );
		$categories = $db->loadObjectList();

		return $categories;
	}
}