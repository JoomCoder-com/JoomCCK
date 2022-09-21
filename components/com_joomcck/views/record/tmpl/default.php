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

<div class="contentpaneopen">
	<?php echo $this->loadTemplate('record_'.$this->menu_params->get('tmpl_article', $this->type->params->get('properties.tmpl_article', 'default')));?>
	
	<div id="comments"><?php echo $this->loadTemplate('comments');?></div>
</div>