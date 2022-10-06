<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$default = $this->default;
$hidden = array();
?>
<?php if($this->params->get('params.total_limit') != 1):?>
<select name="filters[<?php echo $this->key;?>][by]" data-original-title="<?php echo JText::_('CSELECTFILTERCONDITION')?>" rel="tooltip">
	<option value="any" <?php if($this->value && $this->value['by'] == 'any') echo 'selected="selected"';?>><?php echo JText::_('CRECORDHASANYVALUE')?></option>
	<option value="all" <?php if($this->value && $this->value['by'] == 'all') echo 'selected="selected"';?>><?php echo JText::_('CRECORDHASALLVALUES')?></option>
</select>
<br>
<?php endif;?>

<ul class="nav nav-pills" id="flt-<?php echo $this->key;?>-list">
	<?php foreach($this->values as $key => $value): 
		if (!$value->field_value)
			continue;
		$label = $this->_getVal($value->field_value);
		?>
		<li <?php if(in_array($value->field_value, $default) ) echo 'class="active"';?> id="flt-<?php echo $this->id;?>-<?php echo $key;?>"
																						onclick="Joomcck.setHiddenSelectableFlt<?php echo $this->id;?>(this.id, '<?php echo addslashes(htmlspecialchars($value->field_value));?>')">
		<a href="javascript:void(0);">
			<?php echo $label;?>
			<span class="badge bg-light text-muted border"><?php echo ($this->params->get('params.filter_show_number', 1) ? $value->num : NULL);?></span>
		</a>	
		<?php if (in_array($value->field_value, $default)) : ?>
			<input type="hidden" name="filters[<?php echo $this->key;?>][value][]" value="<?php echo htmlspecialchars($value->field_value);?>" id="flt-<?php echo $this->id;?>-<?php echo $key;?>-hid">
		<?php endif;?>
		</li>
	<?php endforeach;?>
</ul>

<script type="text/javascript">
!function($)
{
	Joomcck.setHiddenSelectableFlt<?php echo $this->id;?> = function(id, value)
	{
		var hid=$("#"+id + "-hid");
		if(hid.length > 0)
		{
			$("#"+id + "-hid").remove();
			$("#"+id).removeClass('active');
		}
		else
		{
			var newhid = $(document.createElement("input")).attr({
				 type: "hidden", 
				 value: value, 
				 id: id+"-hid", 
				 name: "filters[<?php echo $this->key;?>][value][]"
				});
			$("#flt-<?php echo $this->key;?>-list").append(newhid);
			$("#"+id).addClass('active');
		}
	}
}(jQuery);
</script>
