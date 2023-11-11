<?php
/**
 * Onyx by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class JFormFieldAccessTable extends JFormField
{

	protected function getInput()
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(true);

		$db->setQuery('SELECT * FROM #__viewlevels WHERE title NOT IN ("Public", "Guest")');
		$accesslevels = $db->loadObjectList();

		$params = new \Joomla\Registry\Registry();
		$params->loadFile(JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/'.$this->element->attributes()->file);
		$params = $params->get($this->fieldname, array());

		$prep_params = array();
		foreach ($params as $param)
		{
			$prep_params[$param->name] = $param;
		}

		$html = '<table class="table">';
		$html .= '<tr>';
		$html .= '<th></th>';
		foreach ($accesslevels as $value)
		{
			$html .= '<th>'.$value->title.'</th>';
		}
		$html .= '</tr>';

		$actions = explode(',', $this->element->attributes()->actions);
		foreach ($actions as $action)
		{
			$html .= '<tr>';
			$html .= '<td>'.ucfirst($action).'</td>';
			foreach ($accesslevels as $value)
			{
				$v = 0;
				$name = $action.$value->id;
				if(isset($this->value->$name))
				{
					$v = $this->value->$name;
				}
				elseif(isset($prep_params[$action]))
				{
					$p = 'default_'.$this->_getSafeAccessLevel($value->title);
					$v = isset($prep_params[$action]->$p) ? $prep_params[$action]->$p : 0;
				}
				$html .= '<td>'.$this->_getSwitcher($name, $v).'</td>';
			}
			$html .= '</tr>';
		}


		$html .= '</table>';

		return $html;
	}

	private function _getSwitcher($name, $value)
	{
		$btn = new JFormFieldRadio();
		$btn->setup(new SimpleXMLElement('<field name="'.$this->name.'['.$name.']" class="btn-group btn-mini" type="radio" default="0"><option value="1" class="btn-mini">ON_ENABLE</option><option value="0" class="btn-mini">ON_DISABLE</option></field>'), $value);
		return $btn->getInput();
	}

	private function _getSafeAccessLevel($str)
	{
		$str = mb_strtolower($str);
		$str = str_replace(' ', '_', $str);
		return $str;
	}

}