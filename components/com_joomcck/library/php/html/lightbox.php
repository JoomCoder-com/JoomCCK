<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class JHTMLLightbox
{
	public static function init()
	{
		$doc = JFactory::getDocument();
		$doc->addScript(JUri::root(TRUE) . '/media/mint/vendors/lightbox2/dist/js/lightbox.min.js');
		$doc->addStyleSheet(JUri::root(TRUE) . '/media/mint/vendors/lightbox2/dist/css/lightbox.min.css');
	}
}