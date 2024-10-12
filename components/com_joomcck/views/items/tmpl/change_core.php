<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString() ?>" method="post" id="adminForm" name="adminForm" class="form-horizontal">
    <?php echo HTMLFormatHelper::layout('item', ['nosave' => 1, 'task_ext' => 'chco']); ?>
    <div class="page-header">
        <h1>
            <img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE); ?>/components/com_joomcck/images/icons/items.png">
            <?php echo \Joomla\CMS\Language\Text::_('C_MASS_CORE_FIELDS'); ?>
        </h1>
    </div>

    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('published'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('published'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('user_id'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('access'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('access'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('meta_index'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('meta_index'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('meta_descr'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('meta_descr'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('meta_key'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('meta_key'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('langs'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('langs'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('featured'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('featured'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('ftime'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('ftime'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('ctime'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('ctime'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('extime'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('extime'); ?></div>
    </div>
    <div class="control-group">
        <div class="form-label"><?php echo $this->form->getLabel('mtime'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('mtime'); ?></div>
    </div>

    <?php foreach($this->cid AS $id): ?>
        <input type="hidden" name="cid[]" value="<?php echo $id ?>"/>
    <?php endforeach; ?>
    <input type="hidden" name="task" value=""/>
    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>