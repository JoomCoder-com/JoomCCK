<form action="<?php echo $action; ?>" method="post" class="form-paypal">
	<?php echo implode("\n", $hiddenfields);?>

	<?php if(!empty($nonehidden)):?>
		<?php foreach($nonehidden AS $label => $field):?>
			<label for=""><?php echo $label;?></label>
			<?php echo $field;?>
		<?php endforeach;?>
	<?php endif;?>
	<button class="btn btn-warning" <?php echo (!JFactory::getUser()->get('id') ? sprintf(' type="button" rel="tooltip" data-original-title="%s"', JText::_('SSI_LOGINTOBUY')) : null); ?> type="submit">
		<img src="<?php echo JURI::root(true);?>/components/com_joomcck/gateways/paypal/paypal.png" title="PayPal" alt="PayPal">
		<?php echo JText::_('SSI_BUYNOW');?>

		<?php if($topay): ?>
			<?php if($topay != $amount): ?>
				<strong>(<s><?php echo $amount; ?></s>)</strong>
			<?php endif;?>
			<strong><?php echo $topay;?></strong><br>
		<?php endif;?>

		<?php if($tax):?>
			<small><small><?php echo JText::sprintf('SSI_WITHOUTTAX', $tax);?></small></small>
		<?php endif; ?>
		<?php if($discount):?>
			<small><small><?php echo JText::sprintf('SSI_WITHDISC', $discount);?></small></small>
		<?php endif; ?>
	</button>
</form>

