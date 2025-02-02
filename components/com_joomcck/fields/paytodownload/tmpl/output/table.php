<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();


$hits = in_array($this->params->get('params.show_hit'), array(3, $this->client ));
$size = in_array($this->params->get('params.show_size'), array(3, $this->client));
$descr = in_array($this->params->get('params.show_descr'), array( 3,  $this->client));
?>
<?php $k = 0;?>
<table class="table table-hover">
	<thead>
		<tr>
			<th>#</th>
			<th><?php echo \Joomla\CMS\Language\Text::_('P_FILE')?></th>
			
			<?php if($hits):?>
				<th><?php echo \Joomla\CMS\Language\Text::_('CHITS')?></th>
			<?php endif;?>

			<?php if($size):?>
				<th><?php echo \Joomla\CMS\Language\Text::_('CSIZE')?></th>
			<?php endif;?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->files AS $i => $file):?>
		<tr>
			<td width="1%"><?php echo $i+1;?></td>
			<td>
				<a <?php if($this->params->get('params.show_in_browser', 0)){ echo ' target="_blank" '; } ?> href="<?php echo $file->url;?>"><?php echo $file->title ? $file->title : $file->realname;?></a>
				<?php if($descr && $file->description):?>
					<p><?php echo $file->description;?></p>
				<?php endif;?>
			</td>
			
			<?php if($hits):?>
				<td width="1%"><?php echo (int)$file->hits?></td>
			<?php endif;?>
			
			<?php if($size):?>
				<td width="1%" nowrap="nowrap"><?php echo HTMLFormatHelper::formatSize($file->size);?></td>
			<?php endif;?>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>

<?php if($this->params->get('params.all_sales_link') && $this->is_seler && !$this->is_free):?>
	<a href="index.php?option=com_joomcck&view=elements&layout=saler&filter_section=<?php echo $this->request->getInt('section_id');?>&Itemid=<?php echo $this->params->get('params.all_sales_iid', $this->request->getInt('Itemid'));?>"
		class="btn btn-sm btn-light border">
		<?php echo \Joomla\CMS\Language\Text::_($this->params->get('params.all_sales_text', 'All sold files'));?>
	</a>
<?php endif; ?>
<?php if($this->is_seler){return;}?>

<?php if($this->is_free):?>
	<div class="alert alert-success"><?php echo \Joomla\CMS\Language\Text::_('P_FREEDOWN');?></div>
<?php else:?>
	<?php if($this->is_paid):?>
		<?php if($this->order->id):?>
			<div class="alert alert-success">
				<p>
					<?php echo \Joomla\CMS\Language\Text::_($this->params->get('params.purchase_title', 'You have already purchased this product'));?>
				</p>
				<?php if($this->params->get('params.all_orders_link')):?>
					<a class="btn btn-sm btn-success" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=elements&layout=buyer&filter_section='.$this->request->getInt('section_id').'&Itemid='.$this->params->get('params.all_orders_iid', $this->request->getInt('Itemid')));?>">
						<?php echo \Joomla\CMS\Language\Text::_($this->params->get('params.all_orders_text', 'My all purchases'));?></a>
				<?php endif; ?>
			</div>
			<?php echo $this->order->table;?>
		<?php endif;?>
	<?php else: ?>
		<?php echo $this->button;?>
		<?php if($this->subscr):?>
			<p>Or <a href="<?php echo EmeraldApi::getLink('emlist', FALSE,  $this->params->get('params.subscription'));?>">Subscribe</a> to get access to everything</p>
		<?php endif;?>
	<?php endif;?>
<?php endif;?>