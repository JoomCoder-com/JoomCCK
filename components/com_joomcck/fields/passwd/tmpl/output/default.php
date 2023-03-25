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

<span id="field_val<?php echo $this->id;?>">
	<?php echo $this->value;?>
	<a href="javascript:void(0);" onclick="ajax_getPasswd<?php echo $this->id;?>();"><?php echo $this->params->get('params.show_label', JText::_('P_SHOWPASW'));?></a>
</span>
<script type="text/javascript">
function ajax_getPasswd<?php echo $this->id;?>()
{
	jQuery.ajax({
		url: Joomcck.field_call_url,
		type:"POST",
		dataType:'json',
		data:{
			field_id: <?php echo $this->id;?>,
			func: "_md5_decrypt",
			field: "passwd",
			record_id: <?php echo $record->id;?>
		}
	}).done(function(json) {
		if(!json.success)
		{
			alert(json.error);
			return;
		}
		jQuery("#field_val<?php echo $this->id;?>").html(json.result);
		setTimeout(function(){jQuery("#field_val<?php echo $this->id;?>").html('<?php echo $this->value; ?>')}, 10000);
	});
}
</script>
