<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined( '_JEXEC' ) or die( 'Restricted access');
jimport('joomla.table.table');

class JoomcckTableVotes extends \Joomla\CMS\Table\Table
{
	public function __construct( &$_db ) {
		parent::__construct( '#__js_res_vote', 'id', $_db );
	}

	public function check()
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$this->ctime = \Joomla\CMS\Factory::getDate()->toSql();
		if(!$this->ip)
		{
			$this->ip = $_SERVER['REMOTE_ADDR'];
		}
		if(!$this->user_id)
		{
			$this->user_id = $user->get('id');
		}

		return TRUE;
	}
	
}
?>
