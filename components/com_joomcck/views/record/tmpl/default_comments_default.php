<?php 
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

if(!$this->comment) return;

$params = $this->tmpl_params['comment'];
$menu = array();

if($this->comment->canedit)
{
	$menu[0] = '<a href="#commentmodal" data-toggle="modal" role="button" onclick="Joomcck.editComment('.$this->comment->id.')">' . JText::_('CEDIT') . '</a>';
}
if($this->comment->candelete)
{
	$menu[2] = JHtml::link('javascript:void(0);', JText::_('CDELETE'), array('onclick' => "deleteComment({$this->comment->id})"));
}
if($this->comment->canmoderate)
{
    if($this->comment->published)
    {
	    $menu[3] = JHtml::link('javascript:void(0);', JText::_('CUNPUB'), array('onclick' => "publishComment({$this->comment->id}, 'unpublish')"));
    }
    else
    {
	    $menu[3] = JHtml::link('javascript:void(0);', JText::_('CPUB'), array('onclick' => "publishComment({$this->comment->id}, 'publish')"));
    }
}

$replay = '';
if($params->get('tmpl_core.comments_nested') > $this->comment->level)
{
	$replay = '<a class="btn btn-sm btn-primary" href="#commentmodal" data-toggle="modal" role="button" onclick="Joomcck.editComment(0, '.$this->comment->id.', '.$this->item->id.')"><i class="icon-comments-2"></i> ' . JText::_('CREPLY') . '</a>';
}

if(!$this->comment->canmoderate && !$this->comment->published)
{
    return;
}
$width = $params->get('tmpl_core.comments_author_avatar_width', 100);
$height = $params->get('tmpl_core.comments_author_avatar_height', 100);
if($this->comment->level > 1)
{
	$width = 50;
	$height = 50;
}
$bc = '';
if($this->comment->rate > 0) $bc = 'bg-info';
if($this->comment->rate < 0) $bc = 'bg-dark';
if($this->comment->rate > 10) $bc = 'bg-success';
?>
<a name="comment<?php echo $this->comment->id?>"></a>
<div style="margin-left: <?php echo $params->get('tmpl_params.comments_indent') * ($this->comment->level - 1)?>px;" id="comment<?php echo $this->comment->id?>-container" class="border p-2 rounded">
	<div class="row">
		<div class="col-md-<?php echo $this->comment->level > 1 ? 1 : 2; ?>">
			<?php if($params->get('tmpl_core.comments_author_avatar')): ?>
				<td width="1%" style="padding-right: 10px;"><img src="<?php echo CCommunityHelper::getAvatar($this->comment->user_id, $width, $height);?>" /></td>
			<?php endif;?>
		</div>
		<div class="col-md-<?php echo $this->comment->level > 1 ? 11 : 10; ?> has-context<?php echo  $this->comment->private ? ' private' : null; echo  !$this->comment->published ? ' published' : null?>">

			<?php if(in_array($this->type->params->get('comments.comments_rate_view', 1), $this->user->getAuthorisedViewLevels())):?>
				<div class="float-end">
					<?php if($this->comment->canrate):?>
						<span id="comment_rate_control_<?php echo $this->comment->id?>">
							<a href="javascript:void(0);" onclick="ajax_rateComment(<?php echo $this->comment->id?>, 1)">
								<?php echo HTMLFormatHelper::icon('plus.png');?>
							</a>
					<?php endif;?>
					<big class="badge <?php echo $bc; ?>" rel="tooltip" data-original-title="<?php echo JText::sprintf('TOTAL_VOTES', $this->comment->rate_num);?>" id="comment_rate_value_<?php echo $this->comment->id?>"><?php echo $this->comment->rate?></big>
					<?php if($this->comment->canrate):?>
							<a href="javascript:void(0);" onclick="ajax_rateComment(<?php echo $this->comment->id?>, 0)">
								<?php echo HTMLFormatHelper::icon('minus.png');?>
							</a>
						</span>
					<?php endif;?>
				</div>
			<?php endif;?>
			
			<div class="float-end">
				<?php echo CEventsHelper::showNum('comment', $this->comment->id);?>
				<?php if($menu || $replay):?>
                    <div class="btn-group float-end" style="display: none;">
						<?php if(isset($replay)):?>
							<?php echo $replay; ?>
						<?php endif;?>
						<?php if($menu):?>
                            <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-sm">
                                <i class="icon-cog"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><?php echo implode('</li><li>', $menu);?></li>
                            </ul>
						<?php endif;?>
                    </div>
				<?php endif;?>
			</div>
			

			<div class="clearfix"></div>
			<div>
				<?php echo $this->comment->comment; ?>
			</div>

            <div>
	            <?php if($this->comment->private): ?>
		            <?php echo HTMLFormatHelper::icon('lock.png', JText::_('CCOMMPRIVATE'));  ?>
	            <?php endif;?>

	            <?php if(!$this->comment->published): ?>
		            <?php echo HTMLFormatHelper::icon('minus-circle.png', JText::_('CCOMMPWAIT'));  ?>
	            <?php endif;?>

	            <?php if($params->get('tmpl_core.comments_username')):?>
                    <small><?php echo JText::sprintf('CWRITTENBY', ($this->comment->name ? $this->comment->name : CCommunityHelper::getName($this->comment->user_id, $this->section))); ?></small>
	            <?php endif;?>

	            <?php if($params->get('tmpl_core.comments_date')):?>
                    <small><?php echo JText::sprintf('CONDATE', JHtml::_('date', $this->comment->created, $params->get('tmpl_core.comments_time_format'))); ?></small>
	            <?php endif;?>
            </div>
			
			<?php if(!empty($this->comment->attachment)):?>
				<b><?php echo JText::_('CATTACH')?></b>:
				<ul>
					<?php foreach ($this->comment->attachment as $attach):?>
						<li>
							<a href="<?php echo $attach->url?>"><?php echo $attach->realname;?></a>
							<?php if($this->type->params->get('comments.comments_attachment_hit')):?>
								<span class="small"><b><?php echo JText::_('CHITS');?>:</b> <span style="color:purple"><?php echo $attach->hits;?></span></span>
							<?php endif;?>
							<?php if($this->type->params->get('comments.comments_attachment_size')):?>
								<span class="small"><b><?php echo JText::_('CSIZE');?>:</b> <span style="color:green"><?php echo HTMLFormatHelper::formatSize($attach->size);?></span></span>
							<?php endif;?>
						</li> 
					<?php endforeach;?>
				</ul>
			<?php endif;?>


			
			<div class="btn-group" style="display: none">
			</div>
		</div>
	</div>
	<div class="height-20"></div>
</div>