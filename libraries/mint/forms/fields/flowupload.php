<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldFlowupload extends JFormField
{
	public $type = 'flowupload';

	public function getInput($params = array())
	{
        $app = JFactory::getApplication();
        $record = ItemsStore::getRecord($app->input->getInt('id'));
        $type = ItemsStore::getType($record->type_id);

        $field = new stdClass();
        $field->id = $app->input->getInt('id');
        $field->params = new JRegistry([
            "params" => [
                "file_formats" => $type->params->get('comments.comments_allowed_formats'),
                "max_size" => $type->params->get('comments.comments_attachment_max')
            ]
        ]);
        $field->iscomment = true;

		$html = JHtml::_('mrelements.flow', $this->name, $this->_getDefault(), $params, $field);
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
				$def[$key] = JArrayHelper::fromObject($value);
			}

			return $def;
		}

		return array();
	}
}
