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

<style>
div.hideinputs{
  display: none;
}
</style>

<div class="btn-group" data-toggle="buttons-radio">
	<button id="fbool-y<?php echo $this->id ?>" type="button" class="btn<?php echo $this->value == 'true' ? ' active btn-primary' : NULL ?>">
		<?php echo $this->labelvalue['true']?>
	</button>
	<button id="fbool-n<?php echo $this->id ?>" type="button" class="btn<?php echo $this->value == 'false' ? ' active btn-primary' : NULL ?>">

		<?php echo $this->labelvalue['false'];?>
	</button>
</div>
<div class="hideinputs">
	<input id="y-input<?php echo $this->id ?>" type="radio" name="filters[<?php echo $this->key;?>]" value="true" <?php echo $this->value == 'true' ? ' checked="checked"' : NULL ?>/>
	<input id="n-input<?php echo $this->id ?>" type="radio" name="filters[<?php echo $this->key;?>]" value="false" <?php echo $this->value == 'false' ? ' checked="checked"' : NULL ?>/>
</div>
<script type="text/javascript">
	(function($){
		var y = $('#fbool-y<?php echo $this->id ?>');
		var n = $('#fbool-n<?php echo $this->id ?>');
		var y_input = $('#y-input<?php echo $this->id ?>');
		var n_input = $('#n-input<?php echo $this->id ?>');
		y.on('click', function(){
			y.addClass('btn-primary');
			n.removeClass('btn-primary');
			y_input.click();
		});
		n.on('click', function(){
			n.addClass('btn-primary');
			y.removeClass('btn-primary');
			n_input.click();
		});
	}(jQuery))

</script>