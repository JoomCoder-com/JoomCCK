<?php
/**
 * by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>
<ul class="nav nav-pills mb-4">
	<li class="nav-item"><a class="nav-link" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&step=1&section_id='.$this->input->get('section_id')); ?>">1. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOAD')?></a></li>
	<li class="nav-item"><a class="nav-link active">2. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTCONFIG')?></a></li>
	<li class="nav-item"><a class="nav-link">3. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTPREVIEW')?></a></li>
	<li class="nav-item"><a class="nav-link">4. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTFINISH')?></a></li>
</ul>

<div class="alert alert-info">
    <ul class="mb-0">
        <li>If you select previously saved import, same articles will be updated and only new articles in import will be created.</li>
        <li>It will update only associated(selected) fields, and will leave other fields untouched should they already contain any data.</li>
    </ul>
</div>

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<div class="mb-3">
		<div class="d-flex gap-2">
			<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $this->presets, 'preset', 'class="form-select"');?>
			<button type="button" class="btn btn-outline-danger d-none" id="delete-preset" title="<?php echo \Joomla\CMS\Language\Text::_('CDELETE'); ?>">
				<i class="fa fa-trash"></i>
			</button>
		</div>
	</div>

	<div class="d-none" id="preset-form">

	</div>

	<div class="form-actions border rounded p-3 mb-4 d-flex justify-content-end">
		<button class="btn btn-primary" type="button" id="next-step" disabled><?php echo \Joomla\CMS\Language\Text::_('CNEXT')?></button>
	</div>
	<input type="hidden" name="task" value="import.preview">
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
			var presetVal = $(this).val();
			// Show delete button only for existing presets (numeric ID), not for "new"
			if(presetVal && presetVal !== 'new' && !isNaN(presetVal)) {
				$('#delete-preset').removeClass('d-none');
			} else {
				$('#delete-preset').addClass('d-none');
			}

			if(presetVal != '') {
				$('#next-step').attr('disabled', true);
				$('#preset-form').html('').hide(function(){
					$.get('<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&layout=params&tmpl=component', false);?>',
						{'preset': preset.val(), type_id: $('input[name="type_id"]').val(), section_id: $('input[name="section_id"]').val()})
					.done(function(data) {
							$('#preset-form').removeClass('d-none').html(data).slideDown('fast', function() {
							$('#importfieldcategory').bind('change', categoryload);
							$('#next-step').removeAttr('disabled');
						});
					});
				});
			} else {
				$('#preset-form').html('').addClass('d-none');
				$('#next-step').attr('disabled', true);
			}
		});

		// Delete preset handler
		$('#delete-preset').on('click', function() {
			var presetId = preset.val();
			if(!presetId || presetId === 'new') return;

			if(!confirm('<?php echo \Joomla\CMS\Language\Text::_('CCONFIRMDELETE'); ?>')) return;

			$.ajax({
				url: '<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=import.deletePreset&tmpl=component&' . \Joomla\CMS\Session\Session::getFormToken() . '=1', false); ?>',
				data: { preset_id: presetId },
				dataType: 'json',
				type: 'POST'
			}).done(function(response) {
				if(response.success) {
					// Remove option from select and reset
					preset.find('option[value="' + presetId + '"]').remove();
					preset.val('').trigger('change');
					alert('<?php echo \Joomla\CMS\Language\Text::_('CDELETED'); ?>');
				} else {
					alert(response.message || 'Error');
				}
			}).fail(function() {
				alert('Error deleting preset');
			});
		});

		window.categoryload =  function() {
			$('#cat-list').html('');
			var field = $('#importfieldcategory').val();
			if(!field || field == '0') return false;

			$('#import-progress').slideDown('fast', function() {
				$.get('<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&layout=categories&tmpl=component', false);?>',
					{'preset': preset.val(), 'field': field, 'section_id':<?php echo $this->section->id;?>})
				.done(function(data) {
					$('#import-progress').slideUp('fast', function() {
						$('#cat-list').html(data);
					});
				});
			})
		}
	}(jQuery))
</script>