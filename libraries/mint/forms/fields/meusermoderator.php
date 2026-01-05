<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/meuser.php';

/**
 * User field for moderator selection - excludes Super Admins
 */
class JFormFieldMeuserModerator extends JFormFieldMeuser
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	public $type = 'MEUserModerator';

	/**
	 * Method to get the users to exclude from the list of users
	 * Excludes Super Admin users since they already have full access
	 *
	 * @return  mixed  Array of users to exclude or null
	 */
	protected function getExcluded()
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(true)
			->select('DISTINCT map.user_id')
			->from('#__user_usergroup_map AS map')
			->join('INNER', '#__usergroups AS g ON g.id = map.group_id')
			->where('g.id = 8'); // Super Users group

		$db->setQuery($query);
		$superAdmins = $db->loadColumn();

		return !empty($superAdmins) ? $superAdmins : null;
	}
}
