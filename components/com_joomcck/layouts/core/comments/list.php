<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

?>

<div id="jcck-comments-list-record" class="card mb-3">
    <div class="card-body">
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

            <div class="pagination">
                <div style="text-align: center;">
				    <?php echo $current->comments_pagination->getPagesCounter(); ?>

				    <?php  if ($current->tmpl_params['comment']->get('tmpl_core.comemnts_limit_box', 1)) : ?>
					    <?php echo $current->comments_pagination->getLimitBox();?>
				    <?php endif; ?>
                </div>
			    <?php echo $current->comments_pagination->getPagesLinks(); ?>
            </div>
            <div class="clearfix"></div>
	    <?php else :?>
		    <?php if($current->tmpl_params['comment']->get('tmpl_core.comments_nocomment', 0) && in_array($current->item->params->get('comments.comments_access_post', $current->type->params->get('comments.comments_access_post')), $current->user->getAuthorisedViewLevels())):?>
                <p class="alert alert-info"><?php echo \Joomla\CMS\Language\Text::_('CMSG_NOCOMMENTSBEFORST');?></p>
		    <?php endif;?>
	    <?php endif;?>
    </div>
</div>

