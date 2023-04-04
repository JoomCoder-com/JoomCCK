<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$img_add = JURI::root(TRUE).'/media/com_joomcck/icons/16/plus-button.png';
$img_edit = JURI::root(TRUE).'/media/com_joomcck/icons/16/pencil.png';
$img_del = JURI::root(TRUE).'/media/com_joomcck/icons/16/cross-button.png';

?>
<style>
#mls_tree .btn-ctrl img {
	max-width: 10px;
}
#mls_tree UL LI {
	padding: 3px;
}
#mls_tree UL LI:hover {
	background-color: lightyellow;
}

textarea {
	margin-top:5px !important;
}
span.hint {
	font-size:10px;
}
.list-level-1 {
	/*margin-left: 20px;*/
}
.list-level-2 {
	margin-left: 20px;
}
.list-level-3 {
	margin-left: 40px;
}
.list-level-4 {
	margin-left: 60px;
}
.list-level-5 {
	margin-left: 80px;
}
</style>

<div id="mls_tree">
	<ul id="parent1" class="unstyled">
	<li id="row1">
		<?php echo JText::_('MLS_ROOT'); ?>
		<img class="btn btn-sm btn-light border" src="<?php echo $img_add?>" align="absmiddle" onclick="mls_input(<?php echo $level?>, 1)" alt="<?php echo JText::_('MLS_ADDCHILD') ?>">
	</li>
		<?php echo implode("\n\t", $result); ?>
	</ul>
</div>

<script type="text/javascript">
(function($){

	window.mls_editvalue = function(value, id)
	{
		$.ajax({
			url: Joomcck.field_call_url,
			dataType: 'json',
			type: 'POST',
			data:{
				field_id: <?php echo $this->id ?>,
				func: "_edit",
				value: value,
				id: id
			}
		}).done(function(json){
			if(!json.success)
			{
				alert(json.error);
				return;
			}
			$("#item"+id).html(value);
		});
	}

	window.mls_edit = function(id)
	{
		if($("#item"+id+ " input").length)
		{
			return;
		}
		var txt = $("#item"+id).html().replace(/"/g, '&quot;');
		$("#item"+id).html("<input type=\"text\" id=\"mls_edit\" value=\""+txt+"\" onkeypress=\"if(event.keyCode == 13) {mls_editvalue(this.value, "+id+");}\" onblur=\"mls_editvalue(this.value, "+id+");\">");
		$("#mls_edit").focus(function(){
		    $(this).select();
		}).focus();
	}

	window.mls_delete = function(id)
	{
		$("#delete_icon").remove();

		$("#row"+id).prepend('<img src="<?php echo JURI::root(TRUE) ?>/components/com_joomcck/images/load.gif" align="absmiddle" id="delete_icon">');
		$.ajax({
			url: Joomcck.field_call_url,
			dataType: 'json',
			type: 'POST',
			data:{
				field_id: <?php echo $this->id ?>,
				func: "_delete",
				field: "multilevelselect",
				record_id: null,
				id: id
			}
		}).done(function(json) {
			if(!json.success)
			{
				alert(json.error);
				return;
			}

			$.each(json.result, function (k, v) {
				if($("#row"+v))
				{
					$("#row"+v).remove();
				}
			});

			$("#delete_icon").remove();
		});
	}

	window.mls_input = function(level, parent_id){
		if($("#mls_inputvalue"))
		{
			$("#mls_inputvalue").remove();
			$("#span-hint").remove();
		}

		var add = $(document.createElement("textarea"))
			.attr({
				id: "mls_inputvalue",
				style:'width:100%;box-sizing:border-box;',
				rows:5
			})
			.appendTo("#row" + parent_id)
			.bind('keyup', function(event){

				var value = $("#mls_inputvalue").val();

				if(event.keyCode == 13 && event.ctrlKey)
				{
					$.ajax({
						url: Joomcck.field_call_url,
						dataType: 'json',
						type: 'POST',
						data:{
							field_id: <?php echo $this->id ?>,
							func: "_savenew",
							name: value,
							level: level,
							parent_id: parent_id
						}
					}).done(function(json){
						if(!json.success)
	        			{
	        				alert(json.error);
	        				return;
	        			}

						$("#mls_inputvalue").remove();
						$("#span-hint").remove();

						out = '';
						$.each(json.result, function(key, val){
							out += '<li id="row' + val.id + '" style="margin-left:' + ((val.level - 1) * 20) + 'px">';
							out += '<div class="btn-group btn-ctrl float-end">';
							out += '<button onclick="mls_input('+ (val.level + 1)+', '+val.id+')" class="btn btn-sm btn-light border"><img src="<?php echo $img_add?>" alt="<?php echo JText::_('MLS_ADDCHILD')?>" align="absmiddle">';
							out += '</button><button onclick="mls_edit('+val.id+')" class="btn btn-sm btn-light border"><img src="<?php echo $img_edit?>" alt="<?php echo JText::_('MLS_EDIT')?>" align="absmiddle">';
							out += '</button><button onclick="mls_delete('+val.id+')" class="btn btn-sm btn-light border"><img src="<?php echo $img_del?>" alt="<?php echo JText::_('MLS_DELETE')?>" align="absmiddle">';
							out += '</button></div><span id="item'+val.id+'">'+val.name+'</span><div class="clearfix"></div></li>';
						});
						$("#row"+parent_id).after(out);
					});
				}
			});

		$("#row" + parent_id).append('<small id="span-hint">Ctrl+Enter to save, separate values by new line</small>');
	}
}(jQuery))
</script>
