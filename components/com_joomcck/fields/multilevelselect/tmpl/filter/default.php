<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>

<script>
var uservalue<?php echo $this->id;?> = 0; 
var levels_set<?php echo $this->id; ?> = 0;<?php echo count($this->values);?>;
var allowed<?php echo $this->id;?> = <?php echo $this->params->get('params.max_levels', 0);?>;
var dfault = [<?php if(count($this->values)) echo implode(', ', $this->values);?>];

var Mls_f<?php echo $this->id; ?> = {};

(function($){

	Mls_f<?php echo $this->id; ?>.getChildren = function(parent_id, level)
	{
		$.each($('#fmls-<?php echo $this->id; ?>-levels').children('div'), function(k, v){
			if((k + 1) >= level) {
				$(this).remove();
			}
		});

		if(!parent_id) {
			return;
		}

		if(parent_id == -1){
			$("#fmls-<?php echo $this->id; ?>-level1").val('');
			return;
		}


		$.ajax({
			url: Joomcck.field_call_url,
			dataType: 'json',
			type: 'POST',
			data:{
				field_id: <?php echo $this->id ?>,
				func: "_drawList",
				field: "multilevelselect",
				filter: true,
				record_id: null,
				level: level,
				parent_id: parent_id, 
				selected: (dfault[(level - 1)] ? dfault[(level - 1)] : 0)
			}
		}).done(function(json) {
			if(!json.success) {
				alert(json.error);
				return;
			}
			
			$("#fmls-<?php echo $this->id; ?>-container"+level).remove();
			var newdiv = $(document.createElement('div'))
			.attr({
				'id': "fmls-<?php echo $this->id; ?>-container"+level, 
				'class': "float-start form-inline", 
				'style':'margin-right:15px'
			});
			newdiv.append(json.result);
			$("#fmls-<?php echo $this->id; ?>-levels").append(newdiv);
			$('#mls-<?php echo $this->id; ?>-level' + level).trigger('change');
			
			levels_set<?php echo $this->id; ?> = level;
			uservalue<?php echo $this->id;?> = (level + 1);
			dfault = []; 
		});
	};
}(jQuery));

</script>
<style>
#fmls-<?php echo $this->id; ?>-levels div.form-inline {
	margin-right: 10px;
	margin-bottom: 8px;
}
</style>
<div id="fmls-<?php echo $this->id; ?>-levels" class="fmls_filter">
	<div id="fmls-<?php echo $this->id; ?>-container1" class="float-start form-inline">
		<?php 
		echo $this->_drawList(array('parent_id' => 1, 'level' => 1, 'filter' => true, 'selected' => (isset($this->values[0]) ? $this->values[0] : false))); ?>
	</div>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
	(function($){
		jQuery('#mls-<?php echo $this->id; ?>-level1').trigger('change');
	}(jQuery))
</script>

