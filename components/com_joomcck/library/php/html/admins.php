<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class JHTMLAdmins
{
	public static function select($name, $active)
	{
		$db =\Joomla\CMS\Factory::getDBO();

		$and = '';
		if ( $reg ) {
		// does not include registered users in the list
			$and = ' AND (gid = 23 OR gid = 24 OR gid = 25)';
		}

		$query = 'SELECT id AS value, name AS text'
		. ' FROM #__users'
		. ' WHERE block = 0'
		. $and
		. ' ORDER BY '. $order
		;
		$db->setQuery( $query );
		if ( $nouser ) {
			$users[] = JHTML::_('select.option',  '0', '- '. \Joomla\CMS\Language\Text::_( 'CNOUSER' ) .' -' );
			$users = array_merge( $users, $db->loadObjectList() );
		} else {
			$users = $db->loadObjectList();
		}

		$users = JHTML::_('select.genericlist',   $users, $name, 'class="form-select" size="1" '. $javascript, 'value', 'text', $active );

		return $users;
	}
}
?>