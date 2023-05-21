<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT. '/libraries/joomla/form/fields/groupedlist.php';

class JHTMLEmerald
{

	public static function plans($name, $include_only = array(), $selected, $descr = '')
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		if(!JFolder::exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_emerald'))
		{
			return '<b>' . JText::_('Please install JoomSubscription extension') . '</b>';
		}

		$db = JFactory::getDBO();
		$query = "SELECT sp.id AS value,
						 sp.name AS text,
						 c.name AS cat_title
				    FROM #__emerald_plans  AS sp
			   LEFT JOIN #__emerald_plans_groups AS c ON c.id = sp.group_id
			    WHERE sp.published = 1 ";

		ArrayHelper::clean_r($include_only);
		if(!empty($include_only))
		{
			$query .= "AND sp.id IN (".implode(',', $include_only).")";
		}

		$query .= " ORDER BY c.id, sp.name";

		$db->setQuery($query);
		$plans = $db->loadObjectList();
		ArrayHelper::clean_r($plans);
		$groups = array();
		foreach($plans as $plan)
		{
			$groups[$plan->cat_title][] = JHtml::_('select.option', $plan->value, $plan->text, 'value', 'text');
		}

		$attr .= sprintf(' size="%s" ', (count($groups)+count($plans) > 10 ? 10 : count($groups)+count($plans)));
		$attr .= ' multiple="multiple"';

		$html[] = '<div><h3>';
		$html[] = JText::_('CRESTRICTIONPLANS');
		$html[] = '</h3>';
		$html[] = '<div class="small">'.JText::_($descr).'</div>';

		$html[] = JHtml::_(
				'select.groupedlist', $groups, $name,
				array(
						'list.attr' => $attr, 'id' => 'value', 'list.select' => $selected, 'group.items' => null, 'option.key.toHtml' => false,
						'option.text.toHtml' => false
				)
		);
		$html[] = '</div>';

		return implode($html);
	}
}
?>