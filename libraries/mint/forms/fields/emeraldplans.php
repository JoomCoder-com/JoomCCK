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
\Joomla\CMS\Form\FormHelper::loadFieldClass('melist');

class JFormFieldEmeraldplans extends \Joomla\CMS\Form\Field\GroupedlistField
{

	public $type = 'Emeraldplans';

	public $multiple = true;

	public $num = 0;

	protected function getInput()
	{
		$this->multiple = true;

		if(!is_dir(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_emerald') || !\Joomla\CMS\Component\ComponentHelper::isEnabled('com_emerald'))
		{
			return '<b>' . \Joomla\CMS\Language\Text::_('Please install JoomSubscription extension') . '</b>';
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
			$db = \Joomla\CMS\Factory::getDBO();

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
				$groups[$plan->cat_title][] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $plan->value, $plan->text, 'value', 'text');
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