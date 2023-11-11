<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<?php if ($this->params->get('params.total_limit')):?>
	<small><?php echo \Joomla\CMS\Language\Text::sprintf('F_OPTIONSLIMIT', $this->params->get('params.total_limit'));?></small>
	<br>
<?php endif; ?>


<?php echo $this->inputvalue;?>
