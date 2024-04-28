<?php

/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<div class="row">
	<div class="col">
        <div class="input-group date mb-3" id="dpfrom<?php echo $this->id; ?>" style="position:relative;">
	  <span class="add-on input-group-addon btn btn-outline-success">
		  <span class="fas fa-calendar"></span> From
	  </span>
            <input <?php echo $this->attr ?> type="text" name="bdpfrom_<?php echo $this->id; ?>" value="" />
        </div>
    </div>
	<div class="col">
        <div class="input-group date" id="dpto<?php echo $this->id; ?>" style="position:relative;">
        <span class="add-on input-group-addon  btn btn-outline-success">
			  <span class="fas fa-calendar"></span> To
		  </span>
            <input <?php echo $this->attr ?> type="text" name="bdpto_<?php echo $this->id; ?>" value="" />

        </div>
    </div>
</div>

<input type="hidden" id="pickerfrom<?php echo $this->id; ?>" class="input" name="jform[fields][<?php echo $this->id; ?>][0]" value="<?php echo $this->default ?>" />
<input type="hidden" id="pickerto<?php echo $this->id; ?>" class="input" name="jform[fields][<?php echo $this->id; ?>][1]" value="<?php echo @$this->value[1] ?>" />

<script type="text/javascript">
	(function($) {
		$('#dpfrom<?php echo $this->id; ?>')
			.datetimepicker({
				format: '<?php echo $this->format; ?>',
				defaultDate: <?php echo $this->default ? "moment('{$this->default}')" : "moment()"; ?>
			})
			.on('dp.change', function(e){
				 $('#dpto<?php echo $this->id; ?>').data("DateTimePicker").minDate(e.date);
				 $('#pickerfrom<?php echo $this->id; ?>').val(e.date.format('<?php echo $this->db_format ?>'));
			})
			.on('dp.error', function(e){
				Joomcck.fieldError(<?php echo $this->id ?>, e.message);
			});
		$('#dpto<?php echo $this->id; ?>')
			.datetimepicker({
				format: '<?php echo $this->format; ?>',
				defaultDate: <?php echo !empty($this->value[1]) ? "moment('{$this->value[1]}')" : "moment()"; ?>
			})
			.on('dp.change', function(e){
				 $('#dpfrom<?php echo $this->id; ?>').data("DateTimePicker").maxDate(e.date);
				 $('#pickerto<?php echo $this->id; ?>').val(e.date.format('<?php echo $this->db_format ?>'));
			})
			.on('dp.error', function(e){
				Joomcck.fieldError(<?php echo $this->id ?>, e.message);
			});
	}(jQuery));
</script>
