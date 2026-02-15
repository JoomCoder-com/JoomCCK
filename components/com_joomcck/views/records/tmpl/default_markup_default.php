<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die('Restricted access');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

$app = \Joomla\CMS\Factory::getApplication();
$markup = $this->tmpl_params['markup'];

// required css file fix issues of UI/UX
\Joomla\CMS\Factory::getDocument()->addStyleSheet(\Joomla\CMS\Uri\Uri::root().'/media/com_joomcck/css/joomcck.css');

?>
<?php if ($markup->get('main.css')): ?>
    <style>
        <?php echo $markup->get('main.css');?>
    </style>
<?php endif; ?>

<?php echo Layout::render('core.markup.header', ['current' => $this]) ?>

<?php echo Layout::render('core.markup.compareBar', ['current' => $this]) ?>

<!-- Show description of the current category or section -->
<?php if ($this->description): ?>
    <div id="jcck-description-block">
	    <?php echo $this->description; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?php echo $this->action; ?>" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php echo Layout::render('core.markup.navbar', ['current' => $this]) ?>

	<?php echo Layout::render('core.markup.filters', ['current' => $this], null, ['client' => 'site', 'component' => 'com_joomcck']) ?>

    <input type="hidden" name="section_id" value="<?php echo $this->state->get('records.section_id') ?>">
    <input type="hidden" name="cat_id" value="<?php echo $app->input->getInt('cat_id'); ?>">
    <input type="hidden" name="option" value="com_joomcck">
    <input type="hidden" name="task" value="">
    <input type="hidden" name="limitstart" value="0">
    <input type="hidden" name="filter_order" value="<?php //echo $this->ordering; ?>">
    <input type="hidden" name="filter_order_Dir" value="<?php //echo $this->ordering_dir; ?>">
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
	<?php if ($this->worns): ?>
		<?php foreach ($this->worns as $worn): ?>
            <input type="hidden" name="clean[<?php echo $worn->name; ?>]" id="<?php echo $worn->name; ?>" value="">
		<?php endforeach; ?>
	<?php endif; ?>
</form>

<!-- Show category index -->
<?php if ($this->show_category_index): ?>
    <div class="clearfix"></div>
	<?php echo $this->loadTemplate('cindex_' . $this->section->params->get('general.tmpl_category')); ?>
<?php endif; ?>

<?php echo Layout::render('core.markup.alphaIndex', ['current' => $this]); ?>

<?php echo Layout::render('core.markup.filterWorns', ['current' => $this]); ?>

<?php if ($this->items): ?>

	<?php echo $this->loadTemplate('list_' . $this->list_template); ?>

	<?php echo Layout::render('core.list.pagination', ['params' => $this->tmpl_params['list'], 'pagination' => $this->pagination]) ?>

<?php elseif ($this->worns): ?>
    <h4 align="center"><?php echo Text::_('CNORECFOUNDSEARCH'); ?></h4>
<?php else: ?>
	<?php echo Layout::render('core.markup.noRecords', ['current' => $this]); ?>
<?php endif; ?>
