<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

// Include the component HTML helpers.
\Joomla\CMS\HTML\HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the tooltip behavior.
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

// required css file fix issues of UI/UX
\Joomla\CMS\Factory::getDocument()->addStyleSheet(\Joomla\CMS\Uri\Uri::root().'/media/com_joomcck/css/joomcck.css');


?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if(task == 'cat.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {

			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(\Joomla\CMS\Language\Text::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=category&section_id=' . \Joomla\CMS\Factory::getApplication()->input->getInt('section_id',0) . '&layout=edit&id=' . (int)$this->item->id); ?>" method="post" name="adminForm" id="item-form"
	  class="form-validate form-horizontal">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>
	<div class="page-header">
		<h1>
			<?php echo empty($this->item->id) ? \Joomla\CMS\Language\Text::_('CNEWCATEGORY') : \Joomla\CMS\Language\Text::sprintf('CEDITCATEGORYS', $this->item->title); ?>
		</h1>
	</div>


	<div id="joomcckContainer">
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'page-details', 'recall' => true, 'breakpoint' => 768]); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-details', \Joomla\CMS\Language\Text::_('COM_JOOMCCK_FIELDSET_DETAILS')); ?>
        <div>
            <div class="control-group">
                <div class="form-label">
					<?php echo $this->form->getLabel('title'); ?>
                </div>
                <div class="controls">
					<?php echo $this->form->getInput('title'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="form-label">
					<?php echo $this->form->getLabel('alias'); ?>
                </div>
                <div class="controls">
					<?php echo $this->form->getInput('alias'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="form-label">
					<?php echo $this->form->getLabel('parent_id'); ?>
                </div>
                <div class="controls">
					<?php echo $this->form->getInput('parent_id'); ?>
                </div>
            </div>
            <legend><?php echo $this->form->getLabel('description'); ?></legend>
			<?php echo $this->form->getInput('description'); ?>


        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-options', \Joomla\CMS\Language\Text::_('COM_JOOMCCK_FIELDSET_OPTIONS')); ?>
        <div>
            <div class="control-group">
                <div class="form-label">
					<?php echo $this->form->getLabel('published'); ?>
                </div>
                <div class="controls">
					<?php echo $this->form->getInput('published'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="form-label">
					<?php echo $this->form->getLabel('access'); ?>
                </div>
                <div class="controls">
					<?php echo $this->form->getInput('access'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="form-label">
					<?php echo $this->form->getLabel('language'); ?>
                </div>
                <div class="controls">
					<?php echo $this->form->getInput('language'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="form-label">
					<?php echo $this->form->getLabel('id'); ?>
                </div>
                <div class="controls">
					<?php echo $this->form->getInput('id'); ?>
                </div>
            </div>
			<?php echo $this->loadTemplate('options'); ?>
        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-general', \Joomla\CMS\Language\Text::_('CGENERAL')); ?>
		<?php echo $this->form->renderFieldSet('general') ?>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-templates', \Joomla\CMS\Language\Text::_('X_SECFSLTMPL')); ?>
		<?php echo $this->form->renderFieldSet('general_tmpl') ?>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>


		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-relative', \Joomla\CMS\Language\Text::_('CRELATIVECAT')); ?>
        <div>
			<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.catselector', 'jform[relative_cats][]', $this->item->section_id, $this->item->relative_cats_ids, 0); ?>
        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-metadata', \Joomla\CMS\Language\Text::_('X_SECFSLMETA')); ?>
        <div>
			<?php echo $this->loadTemplate('metadata'); ?>
        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>


	<div>
		<?php echo $this->form->getInput('section_id'); ?>
		<input type="hidden" name="task" value=""/>
		<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
	</div>
</form>
