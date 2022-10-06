<?php
/**
 * by JoomBoost
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>
<ul class="nav nav-pills">
	<li><a href="<?php echo JRoute::_('index.php?option=com_joomcck&view=import&step=1&section_id='.$this->input->get('section_id')); ?>"><?php echo JText::_('CIMPORTUPLOAD')?></a></li>
	<li class="active"><a><?php echo JText::_('CIMPORTCONFIG')?></a></li>
	<li><a><?php echo JText::_('CIMPORTFINISH')?></a></li>
</ul>

<hr>

<ul>
    <li>If you select previously saved import, same articles will be updated and only new articles in import will be created.</li>
	<li>It will update only associated(selected) fields, and will leave other fields untouched should they already contain any data.</li>
</ul>

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<div class="row">
		<?php echo JHtml::_('select.genericlist', $this->presets, 'preset', 'class="col-md-12"');?>
	</div>

	<div class="hide" id="preset-form">

	</div>

	<div class="form-actions">
		<button class="float-end btn btn-primary" type="button" id="next-step"><?php echo JText::_('CNEXT')?></button>
	</div>
	<input type="hidden" name="task" value="import.import">
	<input type="hidden" name="step" value="3">
	<input type="hidden" name="type_id" value="<?php echo $this->input->get('type_id'); ?>">
	<input type="hidden" name="section_id" value="<?php echo $this->input->get('section_id'); ?>">
	<input type="hidden" name="key" value="<?php echo $this->input->get('key'); ?>">
</form>
<script>
	(function($) {
		var preset = $('#preset');

		$('#next-step').bind('click', function(event) {
			var submit = true;

			if(!preset.val()) {
				alert('Please select import settings or create new...');
				submit = false;
				return false;
			}
			if($('#importname').val() == 0) {
				alert('Enter name');
				submit = false;
				return false;
			}
			$.each($('.cat-select'), function() {
				if(!$(this).val()) {
					alert('category not selected');
					submit = false;
					return false;
				}
			});

			$.each($('div.required select'), function() {
				if($(this).val() == 0) {
					alert('Required fields are not set');
					submit = false;
					return false;
				}
			});

			if($('#importfieldid').val() == 0) {
				alert('Set ID');
				submit = false;
				return false;
			}
			<?php if($this->section->categories || ($this->section->params->get('personalize.personalize', 0) && in_array($this->section->params->get('personalize.pcat_submit', 0), $this->user->getAuthorisedViewLevels()))):?>
			if($('#importfieldcategory').val() == 0) {
				alert('Set Category');
				submit = false;
				return false;
			}
			<?php endif;?>

			<?php if($this->type->params->get('properties.item_title') == 1):?>
			if($('#importfieldtitle').val() == 0) {
				alert('Set title');
				submit = false;
				return false;
			}
			<?php endif;?>

			if(submit) {
                $(this).attr('disabled', 'disabled');
				$('#adminForm').submit();
			}
		});

		preset.bind('change', function() {
			if($(this).val() != '') {
				$('#preset-form').html('').slideUp('fast', function(){
					$.get('<?php echo JRoute::_('index.php?option=com_joomcck&view=import&layout=params&tmpl=component', false);?>',
						{'preset': preset.val(), type_id: $('input[name="type_id"]').val(), section_id: $('input[name="section_id"]').val()})
					.done(function(data) {

							$('#preset-form').html(data).slideDown('fast', function() {
							$('#importfieldcategory').bind('change', categoryload);
						});
					});
				});
			} else {
				$('#preset-form').html('').slideUp('fast');
			}
		});

		window.categoryload =  function() {
			$('#cat-list').html('');
			var field = $('#importfieldcategory').val();
			if(!field) return false;

			$('#progress').slideDown('fast', function() {
				$.get('<?php echo JRoute::_('index.php?option=com_joomcck&view=import&layout=categories&tmpl=component', false);?>',
					{'preset': preset.val(), 'field': field, 'section_id':<?php echo $this->section->id;?>})
				.done(function(data) {
					$('#progress').slideUp('fast', function() {
						$('#cat-list').html(data);
					});
				});
			})
		}
	}(jQuery))
</script>