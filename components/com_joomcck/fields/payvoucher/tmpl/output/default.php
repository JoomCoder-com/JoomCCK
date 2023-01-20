<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<?php if($this->vnum == 0):?>
	<p class="alert alert-danger">
		<?php echo JText::_('PV_VOUCHERSOUTOFSTOCK');?>
    </p>
	<?php return;?>
<?php endif;?>


<?php if($this->params->get('params.all_sales_link') && $this->is_seler && !$this->is_free):?>
	<a href="<?php echo JUri::root()?>index.php?option=com_joomcck&view=elements&layout=saler&filter_section=<?php echo $this->request->getInt('section_id');?>&Itemid=<?php echo $this->params->get('params.all_sales_iid', $this->request->getInt('Itemid'));?>"
		class="btn btn-sm btn-light border">
		<?php echo JText::_($this->params->get('params.all_sales_text', 'All sold files'));?>
	</a>
<?php endif; ?>
<?php if($this->is_seler){return;}?>

<p><?php echo JText::sprintf('PV_VOUCHERSLEFT', $this->vnum);?></p>

<?php if(!empty($this->order->id)):?>
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
	
	<?php if($this->params->get('params.single') == 0 && $this->vnum):?>
		<h3><?php echo JText::_('PV_ORPURCHASEANOTHER')?></h3>
	<?php endif;?>
<?php endif;?>

<?php if(!$this->is_paid || $this->params->get('params.single') == 0):?>
	<?php echo $this->button;?>
<?php endif;?>