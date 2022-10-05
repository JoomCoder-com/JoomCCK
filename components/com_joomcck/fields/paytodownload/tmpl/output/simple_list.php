<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();


$hits = in_array($this->params->get('params.show_hit'), array(3, $this->client ));
$size = in_array($this->params->get('params.show_size'), array(3, $this->client));
$descr = in_array($this->params->get('params.show_descr'), array( 3,  $this->client));
?>

<?php foreach ($this->files AS $i => $file):?>
	<?php $tip = array();?>
	<?php if($hits):?>
		<?php $tip[] = JText::_('CHITS').': <span style="color:purple">'.(int)$file->hits.'</span></b>'; ?>
	<?php endif;?>
	<?php if($size):?>
		<?php $tip[] = JText::_('CSIZE').': <span style="color:green">'.HTMLFormatHelper::formatSize($file->size).'</span></b>'; ?>
	<?php endif;?>
	<?php if($descr && $file->description):?>
		<?php $tip[] ='<p>'.$file->description.'</p>'; ?>
	<?php endif;?>
	<a <?php if($tip) {echo 'class="hasTip" title="::'.htmlspecialchars(implode('<br />', $tip), ENT_COMPAT, 'UTF-8').'"';}?> <?php if($this->params->get('params.show_in_browser', 0)){ echo ' target="_blank" '; } ?> href="<?php echo $file->url;?>"><?php echo $file->title ? $file->title : $file->realname;?></a><?php if(isset($files[$i + 1])):?>, <?php endif;?>
<?php endforeach;?>

<?php if($this->params->get('params.all_sales_link') && $this->is_seler && !$this->is_free):?>
	<a href="index.php?option=com_joomcck&view=elements&layout=saler&filter_section=<?php echo $this->request->getInt('section_id');?>&Itemid=<?php echo $this->params->get('params.all_sales_iid', $this->request->getInt('Itemid'));?>"
		class="btn btn-sm btn-light border">
		<?php echo JText::_($this->params->get('params.all_sales_text', 'All sold files'));?>
	</a>
<?php endif; ?>
<?php if($this->is_seler){return;}?>

<?php if($this->is_free):?>
	<div class="alert alert-success"><?php echo JText::_('P_FREEDOWN');?></div>
<?php else:?>
	<?php if($this->is_paid):?>
		<?php if($this->order->id):?>
			<div class="alert alert-success">
				<p>
					<?php echo JText::_($this->params->get('params.purchase_title', 'You have already purchased this product'));?>
				</p>
				<?php if($this->params->get('params.all_orders_link')):?>
					<a class="btn btn-sm btn-success" href="<?php echo JRoute::_('index.php?option=com_joomcck&view=elements&layout=buyer&filter_section='.$this->request->getInt('section_id').'&Itemid='.$this->params->get('params.all_orders_iid', $this->request->getInt('Itemid')));?>">
						<?php echo JText::_($this->params->get('params.all_orders_text', 'My all purchases'));?></a>
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