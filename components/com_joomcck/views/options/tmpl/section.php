<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$back = NULL;
$user = \Joomla\CMS\Factory::getUser();
if(\Joomla\CMS\Factory::getApplication()->input->getBase64('return'))
{
	$back = Url::get_back('return');
}

$params = $this->section->params;
?>


<div class="page-header">
	<?php if($back):?>
        <div class="float-end">
            <button type="button" class="btn btn-light border" onclick="location.href = '<?php echo $back;?>'">
				<?php echo HTMLFormatHelper::icon('arrow-180.png');  ?>
				<?php echo \Joomla\CMS\Language\Text::_('CBACKTOSECTION'); ?>
            </button>
        </div>
	<?php endif;?>
<h1 class="title">
	<?php echo \Joomla\CMS\Language\Text::sprintf('CUSERSECTIONSETTINGS', $this->section->name);?>
</h1>

</div>

<?php if(!$params->get('personalize.allow_section_set', 1)):?>
<div class="alert alert-danger">
   <h4><?php echo \Joomla\CMS\Language\Text::_('C_SECTIONNOTALLOWEDTOSET')?></h4>
</div>
<?php else :?>

<form name="adminForm" id="adminForm" method="post" class="form-horizontal">

	<div class="row">
		<?php if($params->get('personalize.allow_change_header', 1)):?>
		<div class="mb-3">
			<label for="userTitle" class="form-label"><?php echo \Joomla\CMS\Language\Text::_('CMYHOMEPAGETITLE'); ?></label>
            <input id="userTitle" type="text" class="form-control" name="jform[title]" size="40" value="<?php echo $this->options->get('title');?>">
		</div>
		<?php endif; ?>
		<?php if($params->get('personalize.allow_change_descr', 1)):?>
		<div class="mb-3">
            <label for="jform_description" rel="tooltip" class="form-label" title="<?php echo \Joomla\CMS\Language\Text::_('CMYHOMEPAGEMSGDESCR');?>"><?php echo \Joomla\CMS\Language\Text::_('CMYHOMEPAGEMSG'); ?></label>
            <textarea class="form-control" id="jform_description" name="jform[description]" cols="30" rows="4"><?php echo $this->options->get('description');?></textarea>
		</div>
		<?php endif; ?>

		<?php if($params->get('personalize.allow_access_control_add', 1) && $this->section->params->get('personalize.post_anywhere')): ?>
			<div class="mb-3">
				<label for="userWhoPost" class="form-label"><?php echo \Joomla\CMS\Language\Text::_('CWHOCANPOSTINMYHAMOPAGE'); ?></label>
                <select id="userWhoPost" class="form-select" name="jform[who_post]" >
                    <option value="0" <?php if($this->options->get('who_post') == '0') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CONLYAUTHOR');?></option>
                    <option value="2" <?php if($this->options->get('who_post') == '2') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CEVERYONE');?></option>
					<?php if($this->section->params->get('events.subscribe_user')): ?>
                        <option value="1" <?php if($this->options->get('who_post') == '1') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CONLYSUBSCRIBED');?></option>
					<?php endif;?>
                </select>
			</div>
		<?php endif;?>
		
		<?php if($params->get('personalize.allow_access_control', 1) && $this->section->params->get('events.subscribe_user')): ?>
			<div class="mb-3">
				<label for="userWhoViewBookmarked" class="form-label"><?php echo \Joomla\CMS\Language\Text::_('CWHOCANVIEWMYBOOKMARKS'); ?></label>
                <select id="userWhoViewBookmarked" class="form-select" name="jform[who_view_bookmarked]" >
                    <option value="all" <?php if($this->options->get('who_view_bookmarked') == 'all') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CEVERYONE');?></option>
                    <option value="subscribed" <?php if($this->options->get('who_view_bookmarked') == 'subscribed') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CONLYSUBSCRIBED');?></option>
                </select>
			</div>
			<div class="mb-3">
				<label class="form-label" for="userWhoViewRated"><?php echo \Joomla\CMS\Language\Text::_('CWHOCANVIEWMYRATEDRECORDS'); ?></label>
                <select id="userWhoViewRated" class="form-select" name="jform[who_view_rated]" >
                    <option value="all" <?php if($this->options->get('who_view_rated') == 'all') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CEVERYONE');?></option>
                    <option value="subscribed" <?php if($this->options->get('who_view_rated') == 'subscribed') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CONLYSUBSCRIBED');?></option>
                </select>
			</div>
			<div class="mb-3">
				<label class="form-label" for="userWhoViewCommented"><?php echo \Joomla\CMS\Language\Text::_('CWHOCANVIEWMYCOMMENTEDRECORDS'); ?></label>
                <select id="userWhoViewCommented" class="form-select" name="jform[who_view_commented]" >
                    <option value="all" <?php if($this->options->get('who_view_commented') == 'all') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CEVERYONE');?></option>
                    <option value="subscribed" <?php if($this->options->get('who_view_commented') == 'subscribed') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CONLYSUBSCRIBED');?></option>
                </select>
			</div>
			<div class="mb-3">
				<label for="userWhoViewVisited" class="form-label"><?php echo \Joomla\CMS\Language\Text::_('CWHOCANVIEWMYVISITEDRECORDS'); ?></label>
                <select class="form-select" id="userWhoViewVisited" class="form-select" name="jform[who_view_visited]" >
                    <option value="all" <?php if($this->options->get('who_view_visited') == 'all') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CEVERYONE');?></option>
                    <option value="subscribed" <?php if($this->options->get('who_view_visited') == 'subscribed') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CONLYSUBSCRIBED');?></option>
                </select>
			</div>
			<div class="mb-3">
				<label for="userWhoViewFollowed" class="form-label"><?php echo \Joomla\CMS\Language\Text::_('CWHOCANVIEWMYFOLLOWEDRECORDS'); ?></label>
                <select class="form-select" id="userWhoViewFollowed"  class="form-select" name="jform[who_view_followed]" >
                    <option value="all" <?php if($this->options->get('who_view_followed') == 'all') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CEVERYONE');?></option>
                    <option value="subscribed" <?php if($this->options->get('who_view_followed') == 'subscribed') echo 'selected'; ?> ><?php echo \Joomla\CMS\Language\Text::_('CONLYSUBSCRIBED');?></option>
                </select>
			</div>
		<?php endif;?>
	</div>
			

	<div class="form-actions mb-3">
		<button class="btn btn-outline-success" onclick="Joomla.submitbutton('options.savesectionoptions')">
			<?php echo HTMLFormatHelper::icon('disk.png');  ?>
			<?php echo \Joomla\CMS\Language\Text::_('CSAVE');?>
		</button>

		<?php if($back):?>
			<button class="btn btn-light border" type="button" onclick="Joomla.submitbutton('options.savesectionoptionsclose')">
				<?php echo HTMLFormatHelper::icon('disk--minus.png');  ?>
				<?php echo \Joomla\CMS\Language\Text::_('CSAVECLOSE');?>
			</button>
		<?php endif;?>
	</div>

	<input type="hidden" name="task" value="options.savesectionoptions" />
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
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