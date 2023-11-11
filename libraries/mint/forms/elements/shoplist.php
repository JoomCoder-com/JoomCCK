<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('JPATH_PLATFORM') or die();

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldShopList extends \Joomla\CMS\Form\Field\ListField
{

	protected function getOptions()
	{
		$db = \Joomla\CMS\Factory::getDBO();

		$query = $db->getQuery(true);
		$query->select('id as value, label as text');
		$query->from('#__onyx_shop');
		$query->where('published = 1');
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}
}
