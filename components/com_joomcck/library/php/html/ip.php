<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined ( '_JEXEC' ) or die ( 'Restricted access' );

class JHTMLIp {
	
	public static function country($ip) {
		
		static $results = array ();
		
		if(empty($results[$ip]))
		{
			$db = \Joomla\CMS\Factory::getDBO ();
			
			$query = $db->getQuery ( true );
			$query->select ( 'code, short_code, country' );
			$query->from ( '#__js_ip_2_country' );
			$query->where ( "ip_from <= inet_aton('{$ip}') AND ip_to >= inet_aton('{$ip}')" );
			$db->setQuery($query);
			
			$results[$ip] = $db->loadObject();
		}
		
		if($results[$ip])
		{
			$file = JURI::root()."media/com_joomcck/icons/flag/16/" . strtolower ( $results[$ip]->short_code ) . ".png";
			$options['style'] = 'cursor:pointer';
			$options['onclick'] = "document.getElementById('filter_search').value='country:" . strtolower ( $results[$ip]->code ) . "'; document.adminForm.submit();";
			$options['width'] = 16;
			$options['height'] = 16;
			$options['align'] = 'absmiddle';
			$options['title'] = $results[$ip]->country . " " . Jtext::_ ( 'CCLICKTOFILTER' ) ;
			
			return \Joomla\CMS\HTML\HTMLHelper::image($file, $results[$ip]->country, $options);
		}
	}
	
	public static function block_ip($ip, $id) {
		
		$API = JPATH_ROOT. '/administrator/components/com_jdefender';
		if(@\Joomla\CMS\Filesystem\Folder::exists($API))
		{
			$atr['onclick'] = "document.getElementById('icondefend{$id}').src = '".JURI::root()."administrator/components/com_joomcck/images/load.gif'; xajax_jsrBlockIP('$ip', {$id});";
			$sql = "SELECT COUNT(*) FROM #__jdefender_block_list WHERE type = 'ip' AND `value` = '$ip'";
			$db =\Joomla\CMS\Factory::getDBO();
			$db->setQuery($sql);
			$res = $db->loadResult();
		}
		else
		{
			$atr['onclick'] = "alert('".Jtext::_('CINSTALLDEFENDER')."')";
			$res = 0;
		}
		$atr['align'] = 'absmiddle';
		$atr['style'] = 'cursor:pointer';
		$atr['id'] = 'icondefend'.$id;
		$atr['border'] = 0;

		if($res)
		{
			$img = 'secure_b.png';
			$atr['title'] = Jtext::_('CUNBLOCKIP');
		}
		else
		{
			$img = 'secure.png';
			$atr['title'] = Jtext::_('CBLOCKIP');
		}

		return JHTML::image(JURI::root().'administrator/components/com_joomcck/images/'.$img, Jtext::_('CBLOCKIP'), $atr);
	}
	public static function block_user($user, $id) {
		
		$API = JPATH_ROOT. '/administrator/components/com_jdefender';
		if(@\Joomla\CMS\Filesystem\Folder::exists($API))
		{
			$atr['onclick'] = "document.getElementById('icondefend2{$user}{$id}').src = '".JURI::root()."administrator/components/com_joomcck/images/load.gif'; xajax_jsrBlockUser('$user', {$id});";
			$sql = "SELECT COUNT(*) FROM #__jdefender_block_list WHERE type = 'user' AND `value` = '$user'";
			$db =\Joomla\CMS\Factory::getDBO();
			$db->setQuery($sql);
			$res = $db->loadResult();
		}
		else
		{
			$atr['onclick'] = "alert('".Jtext::_('CBLOKUSERDEFENDER')."')";
			$res = 0;
		}
		$atr['align'] = 'absmiddle';
		$atr['style'] = 'cursor:pointer';
		$atr['id'] = 'icondefend2'.$user.$id;
		$atr['border'] = 0;


		$user = \Joomla\CMS\Factory::getUser($user);
		$user = $user->get('username');


		if($res)
		{
			$img = 'user_secure.png';
			$atr['title'] = Jtext::_('CBLOCKUSER');
		}
		else
		{
			$img = 'user_secure_b.png';
			$atr['title'] = Jtext::_('CUNBLOCKUSER');
		}

		return JHTML::image(JURI::root().'administrator/components/com_joomcck/images/'.$img, $atr['title'], $atr);
	}
}