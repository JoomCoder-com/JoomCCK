<?php
/**
 * by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>
<?php foreach ($this->cols AS $col):?>
	<div class="control-group">
		<label class="form-label" for="type"><?php echo $col;?>
		</label>
		<div class="controls">
			<div class="row">
				<?php if(!empty($this->categories)):?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $this->categories, 'import[category]['.$col.']', 'class="col-md-12 cat-select"', 'id', 'opt', $this->item->params->get('category.'.$col));?>
				<?php endif;?>
				<?php if(!empty($this->usercat)):?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $this->usercat, 'import[ucat]['.$col.']', 'class="col-md-12 cat-select"', 'value', 'text', $this->item->params->get('ucat.'.$col));?>
				<?php endif;?>
			</div>
		</div>
	</div>
<?php endforeach;?>