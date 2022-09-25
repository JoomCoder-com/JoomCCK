<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

include_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components/com_joomcck/library/php/joomcckcomments.php';

class JoomcckCommentsCustom extends JoomcckComments {
	
	public function getNum($type, $record)
	{
		$code = $type->params->get('comments.comment_custom_js_numm');
		$code = str_replace(array('[URL]', '[ID]'), array(\Joomla\CMS\Uri\Uri::getInstance()->toString(), $record->id), $code);
		
		return $code;
	}
	
	public function getComments($type, $record)
	{
		$out[] = '<h2>'.JText::_('CCOMMENTS').'</h2>';
		$code = $type->params->get('comments.comment_custom_js_comm');
		$code = str_replace(array('[URL]', '[ID]'), array(\Joomla\CMS\Uri\Uri::getInstance()->toString(), $record->id), $code);
		
		$out[] = $code;
		
		return implode(" ", $out);
	}
}