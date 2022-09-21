<?php
/**
 * by JoomBoost
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>
<?php foreach ($this->cols AS $col):?>
	<div class="control-group">
		<label class="control-label" for="type"><?php echo $col;?>
		</label>
		<div class="controls">
			<div class="row-fluid">
				<?php if(!empty($this->categories)):?>
					<?php echo JHtml::_('select.genericlist', $this->categories, 'import[category]['.$col.']', 'class="span12 cat-select"', 'id', 'opt', $this->item->params->get('category.'.$col));?>
				<?php endif;?>
				<?php if(!empty($this->usercat)):?>
					<?php echo JHtml::_('select.genericlist', $this->usercat, 'import[ucat]['.$col.']', 'class="span12 cat-select"', 'value', 'text', $this->item->params->get('ucat.'.$col));?>
				<?php endif;?>
			</div>
		</div>
	</div>
<?php endforeach;?>