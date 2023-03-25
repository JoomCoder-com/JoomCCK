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
<ul class="list-group mb-3" id="dates_list<?php echo $this->id; ?>">
<?php foreach($this->value AS $date): ?>
	<li class="list-group-item""><a>
		<span class="float-end mdp-close" style="cursor:pointer;"><?php echo HTMLFormatHelper::icon('cross.png') ?></span>
		<span class="mdp-list"><?php echo $date ?></span>
		<input type="hidden" name="jform[fields][<?php echo $this->id; ?>][]" value="<?php echo $date ?>" /></a>
	</li>
<?php endforeach; ?>
</ul>

<div class="input-group date" id="datetimepicker<?php echo $this->id; ?>" style="position:relative;">
	<input <?php echo $this->attr ?> id="mdpinput<?php echo $this->id; ?>" type="text" class="input" name="bdp_<?php echo $this->id; ?>" value="" />
	<span class="input-group-addon btn btn-outline-success">
		<span class="glyphicon glyphicon-calendar"><?php echo HTMLFormatHelper::icon('calendar-day.png') ?></span>
	</span>
</div>
<?php if($this->params->get('params.max_dates', 0) > 0): ?>
	<small>
		<?php JText::printf('F_MAX_DATE_INFO', $this->params->get('params.max_dates', 0)) ?>
	</small>
<?php endif; ?>

<script type="text/javascript">
	(function($) {
		$('.mdp-list').each(function(i, e){
			var el = $(this);
			el.text(moment(el.text()).format('<?php echo $this->format; ?>'));
		});
		$(document).on('click', '.mdp-close', function(){
			$(this).closest('li').remove();
		});

		var li = $('<li class="list-group-item">').append($.parseHTML(`<a><span class="float-end mdp-close" style="cursor:pointer;"><?php echo HTMLFormatHelper::icon('cross.png') ?></span>
		<span class="mdp-list"></span>
		<input type="hidden" name="jform[fields][<?php echo $this->id; ?>][]" value="" /></a>`));

		var max = parseInt('<?php echo $this->params->get('params.max_dates', 0) ?>');

		$('#datetimepicker<?php echo $this->id; ?>')
			.datetimepicker({
				format: '<?php echo $this->format; ?>'
			})
			.on('dp.change', function(e){
				if(!e.oldDate) {
					return;
				}
				if(max > 0 && $('#dates_list<?php echo $this->id; ?> li').length >= max) {
					Joomcck.fieldError(<?php echo $this->id ?>, "<?php  JText::printf('F_ERROR_MAX', $this->params->get('params.max_dates', 0)) ?>");
					return;
				}
				
				var _li = li.clone();
				$('.mdp-list', _li).text(e.date.format('<?php echo $this->format ?>'));
				$('input', _li).val(e.date.format('<?php echo $this->db_format ?>'));
				$("#dates_list<?php echo $this->id; ?>").append(_li);
				$('#mdpinput<?php echo $this->id; ?>').val('');
			})
			.on('dp.error', function(e){
				Joomcck.fieldError(<?php echo $this->id ?>, e.message);
			});
	}(jQuery));
</script>
