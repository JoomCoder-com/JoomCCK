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
<style>
#filter-collapse,
#vtabs-content,
#tab-ke45f9d3bb288049d6cc4a470829a9e43 {
	overflow: visible;
}
</style>
<div class="input-group date" id="datetimepicker<?php echo $this->id; ?>" style="position:relative;">
	<input <?php echo $this->attr ?> type="text" class="input" name="bdp_<?php echo $this->id; ?>" value="" />
	<span class="input-group-addon">
		<span class="glyphicon glyphicon-calendar"><?php echo HTMLFormatHelper::icon('calendar-day.png') ?></span>
	</span>
</div>
<input type="hidden" value="<?php echo $this->value[0];?>" id="filter_<?php echo $module.$this->id;?>" name="filters[<?php echo $this->key;?>][]">

<script type="text/javascript">
	(function($) {
		$('#datetimepicker<?php echo $this->id; ?>')
			.datetimepicker({
				format: '<?php echo $this->filter_format; ?>'
				<?php echo $this->value[0] ? ", defaultDate: moment('{$this->value[0]}')" : ""; ?>
			})
			.on('dp.change', function(e){
				$('#filter_<?php echo $module.$this->id;?>').val(e.date.format('<?php echo $this->filter_db_format ?>'));
			});
	}(jQuery));
</script>
