<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') || die();?>
<?php if (count($this->values) > 1): ?>
	<ul style="display:inline-block">
		<li><?php echo implode("</li><li>", $this->values); ?></li>
	</ul>
<?php else: ?>
<?php echo $this->values[0]; ?>
<?php endif;?>