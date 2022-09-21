 <?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT. '/components/com_joomcck/library/php/fields/joomcckselectable.php';


class JFormFieldCRadio extends CFormFieldSelectable
{
	public function getInput()
	{
		$params = $this->params;
		$doc = JFactory::getDocument();
		$user = JFactory::getUser();


		$values = array();
		if($params->get('params.sql_source'))
		{
			$values = $this->_getSqlValues();
		}
		else
		{
			$values = explode("\n", $params->get('params.values'));
			ArrayHelper::clean_r($values);

			if(is_array($this->value))
			{
				$this->value = trim(@$this->value[0]);
			}
			if(! in_array($this->value, $values))
			{
				$values[] = $this->value;
			}
			ArrayHelper::clean_r($values);

			if($params->get('params.sort') == 2) asort($values);
			if($params->get('params.sort') == 3) rsort($values);
		}

		$this->values = $values;

		if($this->isnew && $this->params->get('params.default_val'))
		{
			$this->value = $this->params->get('params.default_val');
		}

		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$js = "\n\t\tvar rdo{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[$this->id\\\\]\"]:checked').val();";

		if($this->required)
		{
			$js .= "\n\t\tif(!rdo{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('".addslashes(JText::sprintf("CFIELDREQUIRED", $this->label))."');}";
		}
		return $js;
	}
}
