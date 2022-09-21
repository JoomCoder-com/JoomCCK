<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access'); ?>
<style>
	.mod_sstat { list-style-type: none; line-height: 20px;}
</style>

<div class="mod_sstat">
<?php foreach ($data as $title => $value):?>
	<li><?php echo JText::_(strtoupper($title)) .": $value";?></li>
<?php endforeach;?>
</div>

<div style="clear: both;"></div>