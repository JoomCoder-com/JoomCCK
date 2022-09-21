<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_joomcck/library/php/joomcckcomments.php';

class JoomcckCommentsFacebook extends JoomcckComments {
	
	private function _load($type) {
		static $load = null;
		
		if (! $load) {
			$js = 'window.addEvent("domready", function(d){
			     var js, id = \'facebook-jssdk\', ref = d.getElementsByTagName(\'script\')[0];
			     if (d.getElementById(id)) {return;}
			     js = d.createElement(\'script\'); js.id = id; js.async = true;
			     js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=' . $type->params->get ( 'comments.appid' ) . '";
			     ref.parentNode.insertBefore(js, ref);
			   }(document));';
			
			$doc = JFactory::getDocument ();
			$doc->addScriptDeclaration ( $js );
			$load = TRUE;
			
			if($type->params->get ( 'comments.moder'))
			{
				$doc->setMetaData('fb:admins', $type->params->get('comments.moder'));
			}
		
		}
	}
	public function getNum($type, $item) {
		$this->_load ( $type );
		
		return '<iframe src="http://www.facebook.com/plugins/comments.php?href='.$item->href.'&permalink=1" 
		scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:130px; height:16px;" allowTransparency="true"></iframe>';
	}
	
	public function getComments($type, $item) {
		$this->_load ( $type );
		$out = '<h2>' . JText::_ ( 'CCOMMENTS' ) . '</h2>
		<div class="fb-comments" data-href="' . $item->href . '" data-num-posts="' . $type->params->get ( 'comments.limit', 10 ) . '" data-width="' . $type->params->get ( 'comments.width', 500 ) . '" data-colorscheme="' . $type->params->get ( 'comments.theme', 'light' ) . '"></div><div id="fb-root"></div>';
		return $out;
	}
}