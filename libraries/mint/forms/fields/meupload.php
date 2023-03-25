<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldMeupload extends JFormField
{
	public $type = 'Meupload';

	public function getInput($params = array())
	{
		$html = JHtml::_('mrelements.mooupload', $this->name, $this->_getDefault(), $params);
		return $html;

	}

	private function _getDefault()
	{
		if(!$this->value || !isset($this->value[0]))
		{
			return array();
		}

		if (is_string($this->value[0]))
		{
			$files = JTable::getInstance('Files', 'JoomcckTable');
			return $files->getFiles($this->value, 'filename');
		}

		if (is_object($this->value[0]))
		{
			foreach ($this->value as $key => $value)
			{
				$def[$key] = \Joomla\Utilities\ArrayHelper::fromObject($value);
			}

			return $def;
		}

		return array();
	}
}
