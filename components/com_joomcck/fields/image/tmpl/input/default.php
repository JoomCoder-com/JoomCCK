<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Form\Form;

defined('_JEXEC') or die();
$doc = \Joomla\CMS\Factory::getDocument();





switch ($this->params->get('params.select_type', 0))
{

	case 0: // predefined folder to select from
		echo Layout::render('input.folder', ['current' => $this], $this->layoutFolder);
		break;

	case 1: // joomla core media manager
		echo Layout::render('input.mediamanager', ['current' => $this], $this->layoutFolder);
		break;

	case 2: // upload field
		echo Layout::render('input.file', ['current' => $this], $this->layoutFolder);
		break;

}
