<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die('Restricted access'); ?>
<?php

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal');
?>
<div id="joomcckContainer">
    <form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
		<?php echo HTMLFormatHelper::layout('item', $this); ?>
        <div class="page-header">
            <h1>
				<?php echo empty($this->item->id) ? \Joomla\CMS\Language\Text::_('CNEWSECTION') : \Joomla\CMS\Language\Text::sprintf('CEDITSECTIONS', $this->item->name); ?>
            </h1>
        </div>

        <div>
			<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'page-main', 'recall' => true, 'breakpoint' => 768]); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-main', \Joomla\CMS\Language\Text::_('FS_FORM')); ?>

            <div class="float-start" style="max-width: 500px; min-width:600px; margin-right: 20px;">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('name'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('published'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('access'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('language'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('language'); ?></div>
                </div>
                <div><?php echo $this->form->getInput('description'); ?></div>

            </div>
            <div class="float-start" style="max-width: 500px">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'general', $this->item->params, 'general', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
            </div>

			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-params', \Joomla\CMS\Language\Text::_('FS_GENERAL')); ?>

            <div class="float-start" style="max-width: 600px; margin-right: 20px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'general2', $this->item->params, 'general', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'submission', $this->item->params, 'general', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'general_tmpl', $this->item->params, 'general', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
            </div>
            <div class="float-start" style="max-width: 500px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'search', $this->item->params, 'more', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'general_rss', $this->item->params, 'more', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'metadata', $this->item->params, 'more', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
            </div>

			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-personalize', \Joomla\CMS\Language\Text::_('FS_PERSPARAMS')); ?>

            <div class="float-start" style="max-width: 500px; margin-right: 20px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'persa', $this->item->params, 'personalize', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'persa2', $this->item->params, 'personalize', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'user-section-set', $this->item->params, 'personalize', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
            </div>
            <div class="float-start" style="max-width: 500px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'categories-private-sub', $this->item->params, 'personalize', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'vip', $this->item->params, 'personalize', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
            </div>

			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-events', \Joomla\CMS\Language\Text::_('FS_EVENTPARAMS')); ?>

            <div class="float-start" style="max-width: 500px; margin-right: 20px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'generalevents', $this->item->params, 'events', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
            </div>
            <div class="float-start" style="max-width: 500px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'generalevents2', $this->item->params, 'events', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
            </div>
            <div class="clearfix"></div>
            <div style="max-width: 1000px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'cobevents', $this->item->params, 'events', MFormHelper::FIELDSET_SEPARATOR_HEADER, MFormHelper::STYLE_TABLE); ?>
            </div>

			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
        </div>

        <input type="hidden" name="task" value=""/>
	    <?php echo $this->form->getInput('id');?>
		<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
    </form>
</div>