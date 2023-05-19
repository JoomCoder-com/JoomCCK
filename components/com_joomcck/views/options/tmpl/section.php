<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$back = NULL;
$user = JFactory::getUser();
if(JFactory::getApplication()->input->getBase64('return'))
{
	$back = Url::get_back('return');
}

$params = $this->section->params;
?>


<div class="page-header">
<h1 class="title">
	<?php echo JText::sprintf('CUSERSECTIONSETTINGS', $this->section->name);?>
</h1>
</div>

<?php if(!$params->get('personalize.allow_section_set', 1)):?>
<div class="alert alert-danger">
   <h4><?php echo JText::_('C_SECTIONNOTALLOWEDTOSET')?></h4>
</div>
<?php else :?>

<form name="adminForm" id="adminForm" method="post" class="form-horizontal">
	
	<?php if($back):?>
	<div class="btn-toolbar clearfix">
		<div class="float-end">
		<button type="button" class="btn" onclick="location.href = '<?php echo $back;?>'">
			<?php echo HTMLFormatHelper::icon('arrow-180.png');  ?>
			<?php echo JText::_('CBACKTOSECTION'); ?>
		</button>
		</div>
	</div>
	<div class="clearfix"> </div>
	<?php endif;?>
	
	<div class="row">
		<?php if($params->get('personalize.allow_change_header', 1)):?>
		<div class="control-group">
			<div class="control-label col-md-2"><?php echo JText::_('CMYHOMEPAGETITLE'); ?></div>
			<div class="controls">
				<input type="text" name="jform[title]" size="40" value="<?php echo $this->options->get('title');?>">
			</div>
		</div>
		<?php endif; ?>
		<?php if($params->get('personalize.allow_change_descr', 1)):?>
		<div class="control-group">
			<div class="control-label col-md-2"><span rel="tooltip" data-bs-title="<?php echo JText::_('CMYHOMEPAGEMSGDESCR');?>"><?php echo JText::_('CMYHOMEPAGEMSG'); ?></span></div>
			<div class="controls">
				<textarea id="jform_description" name="jform[description]" cols="30" rows="4"><?php echo $this->options->get('description');?></textarea>
			</div>
		</div>
		<?php endif; ?>

		<?php if($params->get('personalize.allow_access_control_add', 1) && $this->section->params->get('personalize.post_anywhere')): ?>
			<div class="control-group">
				<div class="control-label col-md-2"><?php echo JText::_('CWHOCANPOSTINMYHAMOPAGE'); ?></div>
				<div class="controls">
					<select name="jform[who_post]" >
						<option value="0" <?php if($this->options->get('who_post') == '0') echo 'selected'; ?> ><?php echo JText::_('CONLYAUTHOR');?></option>
						<option value="2" <?php if($this->options->get('who_post') == '2') echo 'selected'; ?> ><?php echo JText::_('CEVERYONE');?></option>
						<?php if($this->section->params->get('events.subscribe_user')): ?>
						<option value="1" <?php if($this->options->get('who_post') == '1') echo 'selected'; ?> ><?php echo JText::_('CONLYSUBSCRIBED');?></option>
						<?php endif;?>
					</select>
				</div>
			</div>
		<?php endif;?>
		
		<?php if($params->get('personalize.allow_access_control', 1) && $this->section->params->get('events.subscribe_user')): ?>
			<div class="control-group">
				<div class="control-label col-md-2"><?php echo JText::_('CWHOCANVIEWMYBOOKMARKS'); ?></div>
				<div class="controls">
					<select name="jform[who_view_bookmarked]" >
						<option value="all" <?php if($this->options->get('who_view_bookmarked') == 'all') echo 'selected'; ?> ><?php echo JText::_('CEVERYONE');?></option>
						<option value="subscribed" <?php if($this->options->get('who_view_bookmarked') == 'subscribed') echo 'selected'; ?> ><?php echo JText::_('CONLYSUBSCRIBED');?></option>
					</select>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label col-md-2"><?php echo JText::_('CWHOCANVIEWMYRATEDRECORDS'); ?></div>
				<div class="controls">
					<select name="jform[who_view_rated]" >
						<option value="all" <?php if($this->options->get('who_view_rated') == 'all') echo 'selected'; ?> ><?php echo JText::_('CEVERYONE');?></option>
						<option value="subscribed" <?php if($this->options->get('who_view_rated') == 'subscribed') echo 'selected'; ?> ><?php echo JText::_('CONLYSUBSCRIBED');?></option>
					</select>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label col-md-2"><?php echo JText::_('CWHOCANVIEWMYCOMMENTEDRECORDS'); ?></div>
				<div class="controls">
					<select name="jform[who_view_commented]" >
						<option value="all" <?php if($this->options->get('who_view_commented') == 'all') echo 'selected'; ?> ><?php echo JText::_('CEVERYONE');?></option>
						<option value="subscribed" <?php if($this->options->get('who_view_commented') == 'subscribed') echo 'selected'; ?> ><?php echo JText::_('CONLYSUBSCRIBED');?></option>
					</select>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label col-md-2"><?php echo JText::_('CWHOCANVIEWMYVISITEDRECORDS'); ?></div>
				<div class="controls">
					<select name="jform[who_view_visited]" >
						<option value="all" <?php if($this->options->get('who_view_visited') == 'all') echo 'selected'; ?> ><?php echo JText::_('CEVERYONE');?></option>
						<option value="subscribed" <?php if($this->options->get('who_view_visited') == 'subscribed') echo 'selected'; ?> ><?php echo JText::_('CONLYSUBSCRIBED');?></option>
					</select>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label col-md-2"><?php echo JText::_('CWHOCANVIEWMYFOLLOWEDRECORDS'); ?></div>
				<div class="controls">
					<select name="jform[who_view_followed]" >
						<option value="all" <?php if($this->options->get('who_view_followed') == 'all') echo 'selected'; ?> ><?php echo JText::_('CEVERYONE');?></option>
						<option value="subscribed" <?php if($this->options->get('who_view_followed') == 'subscribed') echo 'selected'; ?> ><?php echo JText::_('CONLYSUBSCRIBED');?></option>
					</select>
				</div>
			</div>
		<?php endif;?>
	</div>
			

	<div class="form-actions">
		<button class="btn" onclick="Joomla.submitbutton('options.savesectionoptions')">
			<?php echo HTMLFormatHelper::icon('disk.png');  ?>
			<?php echo JText::_('CSAVE');?>
		</button>

		<?php if($back):?>
			<button class="btn" type="button" onclick="Joomla.submitbutton('options.savesectionoptionsclose')">
				<?php echo HTMLFormatHelper::icon('disk--minus.png');  ?>
				<?php echo JText::_('CSAVECLOSE');?>
			</button>
		<?php endif;?>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
<!--

(function( $ ) {
	
	Joomcck.text_limit = function (elem)
	{
		var maxSize = <?php echo $this->section->params->get('personalize.user_sec_descr_length', 200);?>;
		if (elem.value.length > maxSize) {
			elem.value = elem.value.substr(0, maxSize);
		}		
	}
	
})(jQuery);

<?php if($this->section->params->get('personalize.user_sec_descr_length')):?>
	$("#jform_description").keyup(function(){Joomcck.text_limit(this)});
<?php endif; ?>
//-->

</script>
<?php endif; ?>