<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$markup     = $current->tmpl_params['markup'];

?>
<!--  If section is personalized load user block -->
<?php if (($current->section->params->get('personalize.personalize') && $current->input->getInt('user_id')) || $current->isMe): ?>
	<?php echo $current->loadTemplate('user_block'); ?>


	<!-- If title is allowed to be shown -->
<?php elseif ($markup->get('title.title_show')): ?>
	<div class="page-header">
		<?php if (in_array($current->section->params->get('events.subscribe_category'), $current->user->getAuthorisedViewLevels()) && $current->input->getInt('cat_id')): ?>
			<div class="float-end">
				<?php echo HTMLFormatHelper::followcat($current->input->getInt('cat_id'), $current->section); ?>
			</div>
		<?php elseif (in_array($current->section->params->get('events.subscribe_section'), $current->user->getAuthorisedViewLevels())): ?>
			<div class="float-end">
				<?php echo HTMLFormatHelper::followsection($current->section); ?>
			</div>
		<?php endif; ?>
		<h1>
			<?php echo $current->escape(Mint::_($current->title)); ?>
			<?php if ($current->category->id): ?>
				<?php echo CEventsHelper::showNum('category', $current->category->id, true); ?>
			<?php else: ?>
				<?php echo CEventsHelper::showNum('section', $current->section->id, true); ?>
			<?php endif; ?>
		</h1>
	</div>


	<!-- If menu parameters title is set -->
<?php elseif ($current->appParams->get('show_page_heading', 0) && $current->appParams->get('page_heading', '')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $current->escape($current->appParams->get('page_heading')); ?>
		</h1>
	</div>
<?php endif; ?>
