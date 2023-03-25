<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<?php if($this->params->get('params.all_sales_link') && $this->is_seler && !$this->is_free):?>
	<a href="index.php?option=com_joomcck&view=elements&layout=saler&filter_section=<?php echo $this->request->getInt('section_id');?>&Itemid=<?php echo $this->params->get('params.all_sales_iid', $this->request->getInt('Itemid'));?>"
		class="btn btn-sm btn-light border">
		<?php echo JText::_($this->params->get('params.all_sales_text', 'All sold files'));?>
	</a>
<?php endif; ?>
<?php if($this->is_seler){return;}?>


<?php if($this->is_free):?>
	<div class="alert alert-success"><?php echo JText::_($this->params->get('params.free_text', 'CFPTAARTICLEFREE'));?></div>
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
		<?php echo JText::_('CFPTAARTICLEUNLOCK');?>
		<?php echo $this->button;?>
		<?php if($this->subscr):?>
			<p>Or <a href="<?php echo EmeraldApi::getLink('emlist', FALSE,  $this->params->get('params.subscription'));?>">Subscribe</a> to get access to everything</p>
		<?php endif;?>
	<?php endif;?>
<?php endif;?>