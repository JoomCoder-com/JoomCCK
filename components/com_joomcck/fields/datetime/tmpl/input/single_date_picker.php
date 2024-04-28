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

<div class="input-group date" id="datetimepicker<?php echo $this->id; ?>" style="position:relative;">
	<input <?php echo $this->attr ?> type="text" class="input" name="bdp_<?php echo $this->id; ?>" value="" />
	<span class="input-group-addon btn btn-outline-success">
		<span class="fas fa-calendar"></span>
	</span>
</div>
<input type="hidden" id="picker<?php echo $this->id; ?>" class="input" name="jform[fields][<?php echo $this->id; ?>][]" value="<?php echo $this->default ?>" />

<script type="text/javascript">
	(function($) {
		$('#datetimepicker<?php echo $this->id; ?>')
			.datetimepicker({
				format: '<?php echo $this->format; ?>',
				defaultDate: <?php echo $this->default ? "moment('{$this->default}')" : "moment()"; ?>
			})
			.on('dp.change', function(e){
				$('#picker<?php echo $this->id; ?>').val(e.date.format('<?php echo $this->db_format ?>'));
			})
			.on('dp.error', function(e){
				Joomcck.fieldError(<?php echo $this->id ?>, e.message);
			});
	}(jQuery));
</script>
