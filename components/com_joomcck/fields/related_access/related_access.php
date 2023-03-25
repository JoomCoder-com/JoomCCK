<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCRelated_access extends CFormField
{
	public function getInput()
	{
		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$js = FALSE;
		if($this->required)
		{
			$js = "\n\t\tvar bfield_y = jQuery('#boolyes{$this->id}')";
			$js .= "\n\t\tvar bfield_n = jQuery('#boolno{$this->id}')";

			$js .= "\n\t\tif(!bfield_y.prop('checked') && !bfield_n.prop('checked')){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf('CFIELDREQUIRED', $this->label)) . "');}";
		}

		return $js;
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		return (int)($value == 1 ? 1 : ($value == -1 ? -1 : NULL));
	}

	public function onRenderFull($record, $type, $section)
	{
		if($this->value == 1)
		{
			$this->_prepare('full', $record);

			if(!$this->_skip())
			{
				$this->paid = EmeraldApi::hasSubscription($this->plans, Mint::_($this->params->get('params.error_msg')), 0, $this->params->get('params.count'), TRUE, Url::record($record), TRUE);
			}
		}

		return $this->_display_output('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		if($this->value == 1)
		{
			$this->_prepare('list', $record);
			if(!$this->_skip())
			{
				$this->paid = EmeraldApi::hasSubscription($this->plans, NULL, 0, $this->params->get('params.count'), FALSE, NULL, FALSE);
			}
		}

		return $this->_display_output('list', $record, $type, $section);
	}

	private function _skip()
	{
		$user = JFactory::getUser();

		if(@$this->parent->user_id == $user->get('id') && $this->params->get('params.skip_author'))
		{
			return TRUE;
		}

		if(in_array($this->params->get('params.skip_for'), $user->getAuthorisedViewLevels()))
		{
			return TRUE;
		}

		return FALSE;
	}

	private function _prepare($client, $record)
	{
		$db = JFactory::getDbo();

		$this->paid  = NULL;
		$this->plans = array();

		if($this->value == -1)
		{
			return;
		}

		switch($this->params->get('params.relation'))
		{
			case 0:
				$parent = $record->parent_id;
				break;
			case 1:
				$db->setQuery("SELECT field_value
                                FROM `#__js_res_record_values`
                               WHERE record_id = {$record->id}
                                 AND field_type = 'child'
                                 AND field_id = " . $this->params->get('params.field_parent'));
				$parent = (int)$db->loadResult();
				break;
		}

		if(!$parent)
		{
			return;
		}

		$this->parent = ItemsStore::getRecord($parent);
		$fields       = json_decode($this->parent->fields, TRUE);

		$this->plans = array();
		if(!empty($fields[$this->params->get('params.field_plans')]))
		{
			$plans = array_keys($fields[$this->params->get('params.field_plans')]);
			\Joomla\Utilities\ArrayHelper::toInteger($plans);
			$this->plans = $plans;
		}
	}
}