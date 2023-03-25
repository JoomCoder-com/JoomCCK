<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('JPATH_PLATFORM') or die();
jimport('joomla.html.html');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('melist');

class JFormFieldEmeraldplans extends JFormFieldGroupedList
{

	public $type = 'Emeraldplans';

	public $multiple = true;

	public $num = 0;

	protected function getInput()
	{
		$this->multiple = true;

		if(!JFolder::exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_emerald') || !JComponentHelper::isEnabled('com_emerald'))
		{
			return '<b>' . JText::_('Please install Emerald extension') . '</b>';
		}

		if($this->multiple)
		{
			$this->name .= '[]';
		}
		$this->getGroups();
		if($this->num > 10)
		{
			$this->element['size'] = 10;
		}
		else
		{
			$this->element['size'] = $this->num;
		}

		return parent::getInput();
	}

	protected function getGroups()
	{
		static $groups = array();
		static $nums = 0;

		if(empty($groups))
		{
			$db = JFactory::getDBO();

			$query = "SELECT sp.id AS value,
						 sp.name AS text,
						 sp.group_id,
						 g.name AS cat_title
				    FROM #__emerald_plans  AS sp
			   LEFT JOIN #__emerald_plans_groups AS g ON g.id = sp.group_id
			   	   WHERE sp.published = 1
				ORDER BY g.id, sp.name";

			$db->setQuery($query);
			$plans = $db->loadObjectList();
			ArrayHelper::clean_r($plans);

			foreach($plans as $plan)
			{
				$groups[$plan->cat_title][] = JHtml::_('select.option', $plan->value, $plan->text, 'value', 'text');
				$nums ++;
			}
			$nums += count($groups);

			$groups = array_merge(parent::getGroups(), $groups);
		}
		$this->num = $nums;

		ArrayHelper::clean_r($groups);
		return $groups;
	}
}
?>