<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');
if(!$this->values) return;
$default = $this->default;
$hidden = array();
?>
<?php if($this->params->get('params.total_limit') != 1):?>
<select class="form-select mb-3" name="filters[<?php echo $this->key;?>][by]" data-bs-title="<?php echo JText::_('CFILTERCONDITION')?>" rel="tooltip">
	<option value="any" <?php if($this->value && $this->value['by'] == 'any') echo 'selected="selected"';?>><?php echo JText::_('CRECORDHASANYVALUE')?></option>
	<option value="all" <?php if($this->value && $this->value['by'] == 'all') echo 'selected="selected"';?>><?php echo JText::_('CRECORDHASALLVALUES')?></option>
</select>

<?php endif;?>
<?php
foreach($this->values as $key => $value) :
	if (!$value->field_value)
		continue;
	$label = $this->_getVal($value->field_value);
	?>


    <div class="d-inline">
        <input class="btn-check" autocomplete="off" name="filters[<?php echo $this->key;?>][value][]" type="checkbox" id="filterInlineCheckbox<?php echo $this->key;?>-<?php echo $key ?>" value="<?php echo htmlspecialchars($value->field_value);?>" <?php echo (in_array($value->field_value, $default) ? ' checked="checked"' : NULL);?>>
        <label class="btn btn-outline-success" for="filterInlineCheckbox<?php echo $this->key;?>-<?php echo $key ?>"><?php echo $label;?> <span class="badge bg-light text-muted border"><?php echo ($this->params->get('params.filter_show_number', 1) ? $value->num : NULL);?></span></label>
    </div>


<?php endforeach; ?>