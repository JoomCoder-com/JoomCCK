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
		$doc = \Joomla\CMS\Factory::getDocument();
		$doc->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/vendors/lightbox2/dist/js/lightbox.min.js');
		$doc->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/vendors/lightbox2/dist/css/lightbox.min.css');
	}
}