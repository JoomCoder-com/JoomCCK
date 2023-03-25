<?php

/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$filter_id = $module.$this->id;
?>
<style>
#filter-collapse,
#vtabs-content,
#tab-ke45f9d3bb288049d6cc4a470829a9e43 {
	overflow: visible;
}
</style>
<div class="input-append">
	<div class="input-prepend input-group date" id="dpfrom<?php echo $filter_id; ?>" style="position:relative;">
	  <span class="add-on input-group-addon">
		  <span class="glyphicon glyphicon-calendar"><?php echo HTMLFormatHelper::icon('calendar-day.png') ?></span>
	  </span>
	  <input type="text" name="bdpfrom_<?php echo $filter_id; ?>" value="" />
	</div>
	<div class="input-append input-group date" id="dpto<?php echo $filter_id; ?>" style="position:relative;">
			<input type="text" name="bdpto_<?php echo $filter_id; ?>" value="" />
		  <span class="add-on input-group-addon">
			  <span class="glyphicon glyphicon-calendar"><?php echo HTMLFormatHelper::icon('calendar-day.png') ?></span>
		  </span>
	</div>
</div>
<input type="hidden" value="<?php echo @$this->value[0];?>" id="filter0_<?php echo $filter_id;?>" name="filters[<?php echo $this->key;?>][0]">
<input type="hidden" value="<?php echo @$this->value[1];?>" id="filter1_<?php echo $filter_id;?>" name="filters[<?php echo $this->key;?>][1]">

<script type="text/javascript">
	(function($) {
		$('#dpfrom<?php echo $filter_id; ?>')
			.datetimepicker({
                format: '<?php echo $this->filter_format; ?>'
                <?php echo !empty($this->value[0]) ? ", defaultDate: moment('{$this->value[0]}')" : ""; ?>
			})
			.on('dp.change', function(e){
                if(e.oldDate) {
					$('#dpto<?php echo $filter_id; ?>').data("DateTimePicker").minDate(e.date);
                }
				 $('#filter0_<?php echo $filter_id;?>').val(e.date.format('<?php echo $this->filter_db_format ?>'));
			})
			.on('dp.error', function(e){
				Joomcck.fieldError(<?php echo $filter_id ?>, e.message);
			});
		$('#dpto<?php echo $filter_id; ?>')
			.datetimepicker({
				format: '<?php echo $this->filter_format; ?>'
                <?php echo !empty($this->value[1]) ? ", defaultDate: moment('{$this->value[1]}')" : ""; ?>
			})
			.on('dp.change', function(e){
                if(e.oldDate) {
					$('#dpfrom<?php echo $filter_id; ?>').data("DateTimePicker").maxDate(e.date);
                }
				 $('#filter1_<?php echo $filter_id;?>').val(e.date.format('<?php echo $this->filter_db_format ?>'));
			})
			.on('dp.error', function(e){
				Joomcck.fieldError(<?php echo $filter_id ?>, e.message);
			});
	}(jQuery));
</script>
