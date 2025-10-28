<?php 
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Layout\LayoutHelper;
?>

<div class="contentpaneopen">
	<?php
	// Show navigation at top if enabled
	if ($this->navigation->position == 'top' || $this->navigation->position == 'both'):
        echo Layout::render('core.single.recordParts.navigation',['current' => $this]);
	endif;
	?>
	
	<?php echo $this->loadTemplate('record_'.$this->menu_params->get('tmpl_article', $this->type->params->get('properties.tmpl_article', 'default')));?>

	<?php
	// Show navigation at bottom if enabled
	if ($this->navigation->position == 'bottom' || $this->navigation->position == 'both'):
        echo Layout::render('core.single.recordParts.navigation',['current' => $this]);
	endif;
	?>

	<div id="comments" class="mt-5">
        <?php echo $this->loadTemplate('comments');?>
    </div>
</div>