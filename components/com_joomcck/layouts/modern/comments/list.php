<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Comments List Layout
 *
 * Tailwind CSS card + alert replacement for Bootstrap card/alert.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

?>

<div id="jcck-comments-list-record" class="jcck-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-4">
	<div class="jcck-card-body p-4">
		<?php if(count($current->comments)):?>
			<?php foreach ($current->comments AS $comment):?>
				<?php if(empty($comment->id)) continue;?>
				<?php $current->comment = $comment;?>
				<?php echo $current->loadTemplate('comments_'.$current->type->params->get('properties.tmpl_comment', 'default'));?>
				<?php if(!empty($comment->sub_comments)):?>
					<?php foreach ($comment->sub_comments AS $sub_comment):?>
						<?php $current->comment = $sub_comment;?>
						<?php echo $current->loadTemplate('comments_'.$current->type->params->get('properties.tmpl_comment', 'default'));?>
					<?php endforeach;?>
				<?php endif;?>
			<?php endforeach;?>

			<div class="flex flex-wrap items-center justify-between gap-4 mt-4
						[&_.pagination]:flex [&_.pagination]:items-center [&_.pagination]:gap-1
						[&_.page-link]:px-3 [&_.page-link]:py-1.5 [&_.page-link]:text-sm [&_.page-link]:rounded [&_.page-link]:border [&_.page-link]:border-gray-300 [&_.page-link]:text-gray-700 [&_.page-link]:bg-white [&_.page-link]:no-underline
						[&_.page-link:hover]:bg-gray-50
						[&_.active_.page-link]:bg-primary [&_.active_.page-link]:text-white [&_.active_.page-link]:border-primary
						[&_.disabled_.page-link]:opacity-50 [&_.disabled_.page-link]:cursor-not-allowed
						[&_.page-item]:list-none">
				<div class="text-sm text-gray-500 text-center">
					<?php echo $current->comments_pagination->getPagesCounter(); ?>

					<?php if ($current->tmpl_params['comment']->get('tmpl_core.comemnts_limit_box', 1)) : ?>
						<?php echo $current->comments_pagination->getLimitBox();?>
					<?php endif; ?>
				</div>
				<?php echo $current->comments_pagination->getPagesLinks(); ?>
			</div>
		<?php else :?>
			<?php if($current->tmpl_params['comment']->get('tmpl_core.comments_nocomment', 0) && in_array($current->item->params->get('comments.comments_access_post', $current->type->params->get('comments.comments_access_post')), $current->user->getAuthorisedViewLevels())):?>
				<div class="jcck-alert jcck-alert-info flex items-center gap-2 px-4 py-3 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 text-sm">
					<?php echo Text::_('CMSG_NOCOMMENTSBEFORST');?>
				</div>
			<?php endif;?>
		<?php endif;?>
	</div>
</div>
