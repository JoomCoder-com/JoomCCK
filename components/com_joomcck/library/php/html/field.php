<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined ( '_JEXEC' ) or die ( 'Restricted access' );

class JHTMLField {

	public static function state($value, $i, $prefix = '', $enabled = true)
	{
		$states = array(
			1 => array('unpublish', 'JPUBLISHED', 'JLIB_HTML_UNPUBLISH_ITEM', 'JPUBLISHED', true, 'publish', 'publish'),
			0 => array('publish', 'JUNPUBLISHED', 'JLIB_HTML_PUBLISH_ITEM', 'JUNPUBLISHED', true, 'unpublish', 'unpublish')
		);
		
		return JHtml::_('jgrid.state', $states, $value, $i, $prefix, $enabled);
	}
	
	public static function required($value, $i, $prefix = '', $enabled = true)
	{
		$states = array(
			1 => array('notrequired', '', 'XML_LABEL_F_NOTREQ', 'XML_LABEL_F_REQ', true, 'publish', 'publish'),
			0 => array('required', '', 'XML_LABEL_F_REQ', 'XML_LABEL_F_NOTREQ', true, 'unpublish', 'unpublish')
		);
		
		return JHtml::_('jgrid.state', $states, $value, $i, $prefix, $enabled);
	}
	
	public static function searchable($value, $i, $prefix = '', $enabled = true)
	{
		$states = array(
		1 => array('notsearchable', '', 'XML_LABEL_F_NOTSEARCHABLE', 'XML_LABEL_F_SEARCHABLE2', true, 'publish', 'publish'),
		0 => array('searchable', '', 'XML_LABEL_F_SEARCHABLE2', 'XML_LABEL_F_NOTSEARCHABLE', true, 'unpublish', 'unpublish')
		);
	
		return JHtml::_('jgrid.state', $states, $value, $i, $prefix, $enabled);
	}
	
	public static function show_intro($value, $i, $prefix = '', $enabled = true)
	{
		$states = array(
		1 => array('notshow_intro', '', 'XML_LABEL_F_NOT_SHOW_INTRO', 'XML_LABEL_F_SHOW_INTRO', true, 'publish', 'publish'),
		0 => array('show_intro', '', 'XML_LABEL_F_SHOW_INTRO', 'XML_LABEL_F_NOT_SHOW_INTRO', true, 'unpublish', 'unpublish')
		);
	
		return JHtml::_('jgrid.state', $states, $value, $i, $prefix, $enabled);
	}
	
	public static function show_full($value, $i, $prefix = '', $enabled = true)
	{
		$states = array(
		1 => array('notshow_full', '', 'XML_LABEL_F_NOT_SHOW_FULL', 'XML_LABEL_F_SHOW_FULL', true, 'publish', 'publish'),
		0 => array('show_full', '', 'XML_LABEL_F_SHOW_FULL', 'XML_LABEL_F_NOT_SHOW_FULL', true, 'unpublish', 'unpublish')
		);
	
		return JHtml::_('jgrid.state', $states, $value, $i, $prefix, $enabled);
	}
}