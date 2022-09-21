<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<?php if($this->params->get('tmpl_list.type') == 1): ?>
	<ul class="nav nav-pills nav-pills-stacked">
		<li><a><?php echo implode("</li><li>", $this->dates);?></a></li>
	</ul>
<?php else: ?>
	<?php echo implode($this->params->get('tmpl_list.delimiter', ', '), $this->dates);?>
<?php endif;?>	