<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_joomcck/library/php/joomcckcomments.php';

class JoomcckCommentsVk extends JoomcckComments {
	
	private function _load($type) {
		static $load = null;
	
		if (! $load) {
			$js = 'VK.init({
			    apiId: '.$type->params->get('comments.appid').',
			    onlyWidgets: true
			  });';
				
			$doc = JFactory::getDocument ();
			$doc->addScript('//userapi.com/js/api/openapi.js');
			$doc->addScriptDeclaration ( $js );
			
			$load = TRUE;
		}
	}
	
	public function getNum($type, $item) {
		return 0;
	}
	
	public function getComments($type, $item) {
		$this->_load($type);
		
		$pieces[] = 'width:'.$type->params->get ('comments.width', 500);
		$pieces[] = 'limit:'.$type->params->get ('comments.limit', 10);
		$pieces[] = 'pageUrl:"'.Url::record($item).'"';

		$out = '<h2>' . JText::_ ( 'CCOMMENTS' ) . '</h2>';
		$out .= '<div id="vk_comments"></div>
			<script type="text/javascript">
			 VK.Widgets.Comments(\'vk_comments\', {'.implode(', ', $pieces).'}, '.$item->id.');
			</script>';
		
		return $out;
	}
}

