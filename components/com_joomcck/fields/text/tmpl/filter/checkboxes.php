<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
if(!$this->values) return;
$default = $this->default;
?>

<?php
foreach($this->values as $key => $value) :
	if (!$value->field_value)
		continue;
	$label = $this->_getVal($value->field_value);
	?>
	<?php if($key % 2 == 0):?>
	<div>
	<?php endif;?>
	<div class="col-md-6">
		<label class="checkbox">
			<input type="checkbox" name="filters[<?php echo $this->key;?>][value][]" value="<?php echo htmlspecialchars($value->field_value);?>" 
				<?php echo (in_array($value->field_value, $default) ? ' checked="checked"' : NULL);?>> 
			<?php echo $label;?>
			<span class="badge bg-light text-muted border"><?php echo ($this->params->get('params.filter_show_number', 1) ? $value->num : NULL);?></span>
		</label>
	</div>
	<?php if($key % 2 != 0):?>
	</div>
	<?php endif;?>
<?php endforeach; ?>
	

<?php if($key % 2 == 0):?>
	</div>
<?php endif;?>
