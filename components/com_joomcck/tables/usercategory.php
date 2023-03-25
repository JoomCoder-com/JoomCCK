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

class JoomcckTableUsercategory extends JTable
{
	public function __construct( &$_db ) {
		parent::__construct( '#__js_res_category_user', 'id', $_db );
	}
	
    public function bind($array, $ignore = '')
	{
		if(is_array($array))
		{
			if (key_exists('params', $array )) {
				if(is_array($array['params']))
				{
					$registry = new JRegistry();
					$registry->loadArray($array['params']);
					$array['params'] = (string) $registry;
				}
			}
		}
		
		return parent::bind($array, $ignore);
	}
    
	public function check()
	{
		$this->user_id = JFactory::getUser()->get('id');
        $date = JFactory::getDate()->toSql();

		if($this->ctime <= 0){
		    $this->ctime = $date;
		}
		$this->mtime = $date;
		
		$this->alias = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe($this->name);

		return true;
	}
}
?>
