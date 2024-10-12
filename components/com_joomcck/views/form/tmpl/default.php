<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

\Joomla\CMS\HTML\HTMLHelper::_('behavior.keepalive');
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');

$app = \Joomla\CMS\Factory::getApplication();

// Create shortcut to parameters.
//$params = $this->state->get('params');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {


		jQuery('.btn-submit').attr('disabled', 'disabled');
		if (task == 'form.cancel')
		{
			Joomla.submitform(task);
			return;
		}
		var hfid = [];
		var isValid = true;
		var errorText = [];
		//if(task != 'form.cancel' && !document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			//isValid = false;
		}
		<?php if($this->type->params->get('properties.item_title', 1) == 1):?>
			if(!document.getElementById('jform_title').value)
			{
				isValid = false;
				errorText.push('<?php echo \Joomla\CMS\Language\Text::_('CPLEASESELECTTITEL');?>');
				hfid.push('title');
			}
			<?php if($this->type->params->get('properties.item_title_limit')):?>
				if(document.getElementById('jform_title').value.length > <?php echo $this->type->params->get('properties.item_title_limit')?>)
				{
					isValid = false;
					errorText.push('<?php echo \Joomla\CMS\Language\Text::sprintf('C_MSG_TITLETOLONG', $this->type->params->get('properties.item_title_limit', 0));?>');
					hfid.push('title');
				}
			<?php endif; ?>
		<?php endif; ?>

		<?php if($this->section->params->get('personalize.personalize', 0) &&
			in_array($this->section->params->get('personalize.pcat_submit', 0), $this->user->getAuthorisedViewLevels()) &&
			(!$this->item->id && $this->item->user_id || $this->item->id && $this->user->get('id'))): ?>
			if(jQuery('#jform_ucatid :selected').text() == '')
			{
				isValid = false;
				errorText.push('<?php echo \Joomla\CMS\Language\Text::_('CUSERCATSELECT');?>');
				hfid.push('ucat');
			}
		<?php endif; ?>

		<?php if(in_array($this->params->get('submission.allow_category'), $this->user->getAuthorisedViewLevels())&& $this->section->categories):?>

			var catlength = null;
			var cats = jQuery('[name^="jform\\[category\\]"]');

			if(cats.attr('id') == 'category')
			{
				catlength = cats.val().split(',').length;
			}
			else if(cats.attr('id') == 'jformcategory')
			{
				catlength = cats.find('option:selected').length || (cats.val() ? cats.val().length : 0);
			}
			else
			{
				catlength = cats.length;
			}

			if(catlength <= 0 )
			{
				isValid = false;
				errorText.push('<?php echo \Joomla\CMS\Language\Text::_('CPLEASESELECTCAT');?>');
				hfid.push('category');
			}

			<?php if($this->params->get('submission.multi_category', 0)): ?>
				if(catlength > <?php echo  $this->params->get('submission.multi_max_num', 3) ?>)
				{
					isValid = false;
					errorText.push('<?php echo \Joomla\CMS\Language\Text::_('CCATEGORYREACHMAXLIMIT');?>');
					hfid.push('category');
				}
			<?php endif;?>
		<?php endif;?>

		<?php if($this->anywhere) : ?>

			if(jQuery('#posts-list').children('div.alert').length <= 0 )
			{
				isValid = false;
				errorText.push('<?php echo \Joomla\CMS\Language\Text::_('PPLEASEWHERETOPOST');?>');
				hfid.push('anywhere');
			}
		<?php endif;?>

		<?php if($this->multirating && $this->rate_prop > 0):?>
			var ratings = jQuery.parseJSON(jQuery('#multirating').val());
			if(ratings.length < <?php echo $this->rate_prop ?>) {
				isValid = false;
				errorText.push('<?php echo \Joomla\CMS\Language\Text::_('CRATINGREQUIRED');?>');
				hfid.push('rating');
			}
		<?php endif;?>

		<?php if($this->multirating && $this->rate_prop == 0):?>
			if(!jQuery('#jform_votes').val()) {
				isValid = false;
				errorText.push('<?php echo \Joomla\CMS\Language\Text::_('CRATINGREQUIRED');?>');
				hfid.push('rating');
			}
		<?php endif;?>

		<?php foreach ($this->fields AS $field):?>
			/*<?php echo $field->id.' '.$field->title;?>*/
			<?php echo $field->js;?>
		<?php endforeach;?>


		if(!isValid)
		{

			jQuery('.btn-submit').removeAttr('disabled');
			var firsterror = '';
			$.each(hfid, function(idx, el){

				if(idx == 0) firsterror = el;


				Joomcck.fieldError(el, errorText[idx]);
			});

			if(firsterror)
			{
				var tab = jQuery("#field-alert-" + firsterror).closest('.tab-pane');
				if(tab.length) {
					jQuery('a[href="#' + tab.attr('id') + '"]').tab('show');
				}
				var slide = jQuery("#field-alert-" + firsterror).closest('.accordion-body');
				if(slide.length) {
					jQuery('#' + slide.attr('id')).collapse('show');
				}

				jQuery('html, body')
					.animate({
						scrollTop: jQuery("#field-alert-" + firsterror).offset().top
					}, 500);
			}

			//alert('<?php echo $this->escape(\Joomla\CMS\Language\Text::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
			return;
		}

		Joomla.submitform(task);
	};
</script>
<?php if ($this->tmpl_params->get('tmpl_core.form_heading', 1)): ?>
	<div class="page-header">
		<h1>
			<?php if($this->tmpl_params->get('tmpl_core.form_heading_text')):?>
				<?php echo $this->tmpl_params->get('tmpl_core.form_heading_text'); ?>
			<?php else: ?>

                    <?php if($this->item->id):?>
	                    <span class="text-muted"><?php echo \Joomla\CMS\Language\Text::sprintf('CTEDIT', $this->escape($this->type->name)); ?></span> <?php echo $this->item->title ?>
                    <?php else: ?>
	                    <?php echo \Joomla\CMS\Language\Text::sprintf('CTSUBMIT', $this->escape($this->type->name)); ?>
                    <?php endif; ?>

				<?php if(!empty($this->parent->title)):?>
					- <?php echo $this->parent->title; ?>
				<?php endif; ?>
			<?php endif; ?>
		</h1>
	</div>
<?php endif; ?>

<div class="alert alert-warning" style="display:none" id="form-error"></div>

<?php if($this->type->description):?>

<div class="my-4">
	<?php echo $this->type->description;?>
</div>

<?php endif; ?>

<?php if(!$this->user->get('id') && $this->type->params->get('submission.public_alert') && ($this->type->params->get('submission.public_edit') == 0)):?>
	<div class="alert alert-warning"><?php echo $this->tmpl_params->get('tmpl_core.form_public_alert', \Joomla\CMS\Language\Text::_('CNOTREGISTERED'));?></div>
	<br />
<?php endif;?>

<form method="post" action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString()?>" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
	<?php if(in_array($this->tmpl_params->get('tmpl_core.form_button_position', 1), array(1,3))):?>
		<div class="clearfix mb-3">
			<?php echo $this->loadTemplate('buttons');?>        </div>
	<?php endif;?>

	<?php echo $this->loadTemplate('form_'.$this->params->get('properties.tmpl_articleform'));?>

	<?php if($this->tmpl_params->get('tmpl_core.form_captcha', 1) && !$this->user->get('id')):?>
		<div class="form-horizontal">
			<div class="control-group">
				<label class="form-label">&nbsp;</label>
				<div class="controls">
					<?php  echo $this->form->getInput('captcha'); ?>
				</div>
			</div>
		</div>
	<?php endif;?>

	<?php if(in_array($this->tmpl_params->get('tmpl_core.form_button_position', 1), array(2,3))):?>
		<div class="mt-3">
			<?php echo $this->loadTemplate('buttons');?>
        </div>
	<?php endif;?>

	<?php echo $this->form->getInput('section_id'); ?>
	<?php echo $this->form->getInput('type_id'); ?>
	<?php echo $this->form->getInput('id'); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $app->input->getInt('id', 0);?>" />
	<input type="hidden" name="Itemid" value="<?php echo $app->input->getInt('Itemid');?>" />
	<input type="hidden" name="return" value="<?php echo $app->input->getBase64('return');?>" />
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_( 'form.token' ); ?>
</form>



<script type="text/javascript">
	jQuery('input[type="text"][class!="textboxlist-bit-editable-input"]').keydown(function(e){
		if(e.keyCode == 13){
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
	});
	<?php if($h = $app->getUserState('com_joomcck.fieldhighlights')):?>
		<?php foreach ($h AS $field_id => $msg):?>
			Joomcck.fieldError(<?php echo $field_id?>, '<?php echo $msg?>');
		<?php endforeach;?>
		<?php $app->setUserState('com_joomcck.fieldhighlights', NULL);?>
	<?php endif;?>
</script>
