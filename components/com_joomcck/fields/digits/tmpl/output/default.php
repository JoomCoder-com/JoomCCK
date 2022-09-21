<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$value = number_format($this->value, $this->params->get('params.decimals_num'), $this->params->get('params.dseparator', ''), $this->params->get('params.separator', ''));
?>

<?php echo $this->params->get('params.prepend');?>

<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8');?>

<?php echo $this->params->get('params.append');?>