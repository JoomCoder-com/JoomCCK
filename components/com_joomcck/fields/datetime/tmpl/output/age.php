<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') || die();
$start = JFactory::getDate($this->value[0]);
if (count($this->value) == 2) {
    $end   = JFactory::getDate($this->value[1]);
} else {
    $end   = JFactory::getDate();
}
$age   = $start->diff($end)->y;
JText::printf('F_YEARS_AGE', $age);
