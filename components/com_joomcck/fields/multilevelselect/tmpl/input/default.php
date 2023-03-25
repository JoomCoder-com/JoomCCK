<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$params = $this->params;
$img_add = JURI::root(TRUE).'/media/mint/icons/16/plus-button.png';
$img_edit = JURI::root(TRUE).'/media/mint/icons/16/pencil.png';
$img_del = JURI::root(TRUE).'/media/mint/icons/16/cross-button.png';
?>
<style>
	#mls-<?php echo $this->id; ?>-levels select {
		max-width: 120px;
	}
	.default_mlsvalues .alert {
		margin-bottom: <?php echo $params->get('params.max_values') > 1 ? '10' : '0'?>px;
	}
</style>

<script>

var Mls<?php echo $this->id; ?> = {};
var allowed<?php echo $this->id; ?> = <?php echo $params->get('params.max_levels');?>;

(function($){

	Mls<?php echo $this->id; ?>.edit = function(el, level, parent_id) {
		var select = $(el).parent('div.float-start.form-inline').children('select');
		if(select.val() == 0){
			Joomcck.fieldError('<?php echo $this->id; ?>', '<?php echo htmlentities(JText::_('MLS_CHOSESOMETHING'), ENT_QUOTES, 'UTF-8') ?>');
			return;
		}
		var text = select.children('option:selected').text();
		text = text.replace(/ \([0-9]*\)$/g, '');

		$('#mls-<?php echo $this->id; ?>-level'+level+', #add-button, button.btn-edit').hide();

		var input = $(document.createElement('input'))
			.attr({type:'text','rel':select.val()})
            .addClass('form-control')
			.val(text)
			.bind('keyup', function(event){
				if(event.keyCode == 13){
	        		Mls<?php echo $this->id; ?>.editValue(level, parent_id);
				}
				if(event.keyCode == 27){
	        		Mls<?php echo $this->id; ?>.deleteFormEdit(level);
				}
			});

		$(document.createElement('span'))
			.attr('id', 'new-form')
			.addClass('input-group')
			.append(input)
			.append('<a class="btn btn-outline-primary" onclick="Mls<?php echo $this->id; ?>.editValue(' + level + ', ' + parent_id + ')"><i class="fas fa-plus"></i></a>')
			.append('<a class="btn btn-outline-danger" onclick="Mls<?php echo $this->id; ?>.deleteFormEdit('+level+')"><i class="fas fa-minus"></i></a>')
			.appendTo($("#mls-<?php echo $this->id; ?>-container"+level));

		input.focus(function(){
			$(this).select();
		}).focus();

	}
	Mls<?php echo $this->id; ?>.editValue = function(level, parent_id)
	{
		var text = $('#new-form').children('input').val();
		$.ajax({
			url: Joomcck.field_call_url,
			type:"post",
			dataType: 'json',
			data:{
				field_id: <?php echo $this->id ?>,
				func: "_editvalue",
				name: text,
				mlsid: $('#new-form').children('input').attr('rel'),
				level: level,
				parent_id: parent_id
			}
		}).done(function(json) {

			if(json.error)
			{
				alert(json.error);
				return;
			}

			Mls<?php echo $this->id; ?>.deleteFormEdit(level);
			$('#mls-<?php echo $this->id; ?>-level'+level).children('option:selected').text(text);
		});
	}


	Mls<?php echo $this->id; ?>.checkForm = function(e) {
		if(<?php echo (int)$params->get('params.max_values')?> <= 0) {
			return;
		}
		if(<?php echo ($params->get('params.max_values') == 1) ? 1 : 0 ?>){
			return;
		}

		var length = $('#mlsvalues-list<?php echo $this->id; ?>').children('div.alert').length;


		e = e || [];
		if(e.type == 'closed') {
			length -= 1;
		}

		if(<?php echo (int)$params->get('params.max_values')?> 	<= length) {
			$('#mls-<?php echo $this->id; ?>-form-box').css('display', 'none');
			$("#mls-<?php echo $this->id; ?>-input").remove();
			$.each($('#mls-<?php echo $this->id; ?>-levels').children('div.form-inline'), function(k, v){
				if((k + 1) > 1) {
					$(v).remove();
				} else {
					$('#mls-44-level1').attr('name','overlimit');
				}
			});
		} else {
			$('#mls-44-level1').attr('name','jform[fields][44][levels][]');
			$('#mls-<?php echo $this->id;?>-form-box').css('display', 'block');
		}
	}

	Mls<?php echo $this->id; ?>.addItem = function() {
		var fields 	= $('[name^="jform\\[fields\\]\\[<?php echo $this->id; ?>\\]\\[levels\\]"]');
		var added 	= {};
		var title 	= [];
		var ids 	= [];
		var noval 	= 0;
		try {
			$.each(fields, function(key, val){
				if(parseInt(this.value) > 0)
				{
					ids[key] = this.value;
					added[this.value] = title[key] = this.options[this.selectedIndex].text.replace(/(?: \([0-9]+\))$/, '');
				}
				else
				{
					noval ++;
				}
			});

			<?php if($params->get('params.min_levels_req')):?>
			if(<?php echo $params->get('params.min_levels_req');?> > ids.length) {
				throw '<?php echo JText::_('MLS_LEVELREQUIRED');?>';
			}
			<?php endif;?>

			if(ids.length)
			{
				added = JSON.stringify(added);

				$.each($('[name^="jform\\[fields\\]\\[<?php echo $this->id; ?>\\]"]'), function(key, val){
					if(key == 'levels') return true;
					if(this.value == added) {
						throw '<?php echo JText::_('MLS_VALUEEXISTS');?>';
					}
				});

				var el = $(document.createElement('div'))
					.attr('class', 'alert alert-info alert-dismissible fade show')
                    .attr('role','alert')
					.html(
						title.join('<?php echo $this->params->get('params.separator', ' ');?> ') +
						'<input type="hidden" name="jform[fields][<?php echo $this->id;?>][]" value="'+added.replace(/"/g, '&quot;')+'">'+'<button class="btn-close" data-bs-dismiss="alert" type="button"></button>')
					.bind('closed', Mls<?php echo $this->id; ?>.checkForm);

				$('#mlsvalues-list<?php echo $this->id; ?>').append(el);
				//$('#mls-9-container1').children('select').val('');

				Mls<?php echo $this->id; ?>.checkForm();

				//Mls<?php echo $this->id; ?>.getChildren(-1, 2);
			}
		}
		catch(e)
		{
			alert(e);
		}
	}

	Mls<?php echo $this->id; ?>.getChildren = function(parent_id, level)
	{

		$("#mls-<?php echo $this->id; ?>-input").remove();
		$.each($('#mls-<?php echo $this->id; ?>-levels').children('div'), function(k, v){
			if((k + 1) >= level) {
				//this.value = -1;
				//console.log(this)
				$(this).remove();
			}
		});

		if(parent_id == 0) return;
		/*
		if(parent_id == 0){
			var mls_levels = $('[name^="jform\\[fields\\]\\[<?php echo $this->id;?>\\]\\[levels\\]"]');
			jQuery.each(mls_levels, function(k, v){
					v.value = 0;
				});
			return;
		}*/

		$.ajax({
			url: Joomcck.field_call_url,
			dataType: 'json',
			type: 'POST',
			data:{
				field_id: <?php echo $this->id ?>,
				func: "_drawList",
				filter: null,
				level: level,
				parent_id: parent_id
			}
		}).done(function(json) {
			if(!json.success) {
				alert(json.error);
				return;
			}

			$("#mls-<?php echo $this->id; ?>-container"+level).remove();
			$("#mls-<?php echo $this->id; ?>-levels").append(
				$(document.createElement('div'))
					.attr({
						'id': "mls-<?php echo $this->id; ?>-container"+level,
						'class': "float-start form-inline",
						'style':'margin-right:15px; margin-bottom:5px'
					})
					.html(json.result)
			);
		});
	}

	Mls<?php echo $this->id; ?>.renderInput = function(level, parent_id)
	{
		$("#mls-<?php echo $this->id; ?>-input").remove();
		$.each($('#mls-<?php echo $this->id; ?>-levels').children('div'), function(k, v){
			if((k + 1) > level) {
				$(this).remove();
			}
		});

		/*if(allowed<?php echo $this->id; ?> && allowed<?php echo $this->id; ?> < $('#mls-<?php echo $this->id; ?>-levels').children('div').length)
		{
			alert("<?php echo JText::_('MLS_MAXLEVELSREACHED')?>");
			return;
		}*/

		$('#mls-<?php echo $this->id; ?>-level'+level+', #add-button, button.btn-edit').hide();

		var input = $(document.createElement('input'))
			.attr({type:'text'})
            .addClass('form-control')
			.bind('keyup', function(event){
				if(event.keyCode == 13){
	        		Mls<?php echo $this->id; ?>.addValue(level, parent_id);
				}
				if(event.keyCode == 27){
	        		Mls<?php echo $this->id; ?>.deleteForm(level);
				}
			});

		$(document.createElement('span'))
			.attr('id', 'new-form')
			.addClass('input-group')
			.append(input)
			.append('<a class="btn btn-outline-primary" onclick="Mls<?php echo $this->id; ?>.addValue(' + level + ', ' + parent_id + ')"><i class="fas fa-plus"></i></a>')
			.append('<a class="btn btn-outline-danger" onclick="Mls<?php echo $this->id; ?>.deleteForm('+level+')"><i class="fas fa-minus"></i></a>')
			.appendTo($("#mls-<?php echo $this->id; ?>-container"+level));
	}

	Mls<?php echo $this->id; ?>.deleteForm = function(level)
	{
		$("#mls-<?php echo $this->id; ?>-level"+level).val('');
		$("#new-form").remove();
		$('#mls-<?php echo $this->id; ?>-level'+level+', #add-button, button.btn-edit').show();
	}
	Mls<?php echo $this->id; ?>.deleteFormEdit = function(level)
	{
		$("#new-form").remove();
		$('#mls-<?php echo $this->id; ?>-level'+level+', #add-button, button.btn-edit').show();
	}

	Mls<?php echo $this->id; ?>.addValue = function(level, parent_id)
	{
		$.ajax({
			url: Joomcck.field_call_url,
			type:"post",
			dataType: 'json',
			data:{
				field_id: <?php echo $this->id ?>,
				func: "_savenew",
				name: $('#new-form').children('input').val(),
				level: level,
				parent_id: parent_id
			}
		}).done(function(json) {

			if(json.error)
			{
				alert(json.error);
				return;
			}

			Mls<?php echo $this->id; ?>.deleteForm(level);


			$.each(json.result, function(k, v){
				if($("#mls-<?php echo $this->id; ?>-level" + level + ' option[value='+v.id+']').length <= 0) {
					$("#mls-<?php echo $this->id; ?>-level"+level)
						.prepend('<option value="' + v.id + '">' + v.name + '</option>')
				}
				$("#mls-<?php echo $this->id; ?>-level"+level).val(v.id);

				if(allowed<?php echo $this->id; ?> && allowed<?php echo $this->id; ?> > level) {
					Mls<?php echo $this->id; ?>.getChildren(v.id, (level + 1));
				}
			});
		});
	}

	$.each($('#mlsvalues-list<?php echo $this->id; ?>').children('div.alert'), function(k, v){
		$(this).bind('closed', Mls<?php echo $this->id; ?>.checkForm);
	});
}(jQuery));
</script>
<?php if($params->get('params.max_values') != 1): ?>
	<div class="default_mlsvalues" id="mlsvalues-list<?php echo $this->id; ?>">
		<?php if (!empty($this->value) && count($this->value) > 0): ?>
			<?php
			foreach ( $this->value as $item ):
				$title = implode($params->get('params.separator', ' '), $item);
				$id = implode('-', array_keys($item));
			?>
			<div class="alert alert-info alert alert-info alert-dismissible fade show" role="alert" id="mlsval-<?php echo $id;?>">

				<?php echo $title;?>
				<input type="hidden" name="jform[fields][<?php echo $this->id;?>][]" value="<?php echo htmlentities(json_encode($item), ENT_QUOTES, 'UTF-8');?>">

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

			</div>
			<?php endforeach;?>
		<?php endif; ?>
	</div>
<?php endif; ?>


<div id="mls-<?php echo $this->id;?>-form-box">
	<?php if($params->get('params.max_values') > 1): ?>
		<small class="text-muted">
			<?php echo JText::sprintf('F_OPTIONSLIMIT', $params->get('params.max_values')); ?>
		</small>
		<br><br>
	<?php endif;?>

	<div id="mls-<?php echo $this->id; ?>-levels">
		<?php if($params->get('params.max_values') == 1 && $this->value): ?>
			<?php
			$k = $parent = 1;
			foreach ( $this->value[0] as $id => $name ):
			?>
			<div id="mls-<?php echo $this->id; ?>-container<?php echo $k;?>" class="float-start form-inline" style="margin-right:15px;margin-bottom: 5px;">
				<?php echo $this->_drawList(array('parent_id' => $parent, 'level' => $k, 'selected' => $id, 'filter' => 0)); ?>
			</div>
			<?php $parent = $id; $k++; endforeach;?>
			<?php if($this->params->get('params.max_levels') >= $k):?>
				<div id="mls-<?php echo $this->id; ?>-container<?php echo $k;?>" class="float-start form-inline" style="margin-right:15px;margin-bottom: 5px;">
					<?php echo $this->_drawList(array('parent_id' => $parent, 'level' => $k, 'filter' => 0)); ?>
				</div>
			<?php endif;?>
		<?php else:?>
			<div id="mls-<?php echo $this->id; ?>-container1" class="float-start form-inline" style="margin-right:15px;margin-bottom: 5px;">
				<?php echo $this->_drawList(array('parent_id' => 1, 'level' => 1, 'filter' => 0)); ?>
			</div>
		<?php endif;?>
	</div>

	<?php if($params->get('params.max_values') > 1): ?>
        <button type="button" id="add-button" class="btn btn-outline-success" onclick="Mls<?php echo $this->id; ?>.addItem();">
			<i class="fas fa-plus"></i> <?php echo JText::_('MLS_ADD');?>
        </button>
	<?php endif;?>
</div>
<script type="text/javascript">
	Mls<?php echo $this->id; ?>.checkForm();
	jQuery('#mlsvalues-list<?php echo $this->id; ?> div.alert').bind('closed', Mls<?php echo $this->id; ?>.checkForm);
</script>
