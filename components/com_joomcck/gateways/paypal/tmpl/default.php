<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

?>
<form action="<?php echo $action; ?>" method="post" class="form-paypal">
	<?php echo implode("\n", $hiddenfields);?>

	<?php if(!empty($nonehidden)):?>
		<?php foreach($nonehidden AS $label => $field):?>
			<label for=""><?php echo $label;?></label>
			<?php echo $field;?>
		<?php endforeach;?>
	<?php endif;?>
	<button class="btn btn-warning" <?php echo (!\Joomla\CMS\Factory::getUser()->get('id') ? sprintf(' type="button" rel="tooltip" data-original-title="%s"', \Joomla\CMS\Language\Text::_('SSI_LOGINTOBUY')) : null); ?> type="submit">
		<img src="<?php echo JURI::root(true);?>/components/com_joomcck/gateways/paypal/paypal.png" title="PayPal" alt="PayPal">
		<?php echo \Joomla\CMS\Language\Text::_('SSI_BUYNOW');?>

		<?php if($topay): ?>
			<?php if($topay != $amount): ?>
				<strong>(<s><?php echo $amount; ?></s>)</strong>
			<?php endif;?>
			<strong><?php echo $topay;?></strong><br>
		<?php endif;?>

		<?php if($tax):?>
			<small><small><?php echo \Joomla\CMS\Language\Text::sprintf('SSI_WITHOUTTAX', $tax);?></small></small>
		<?php endif; ?>
		<?php if($discount):?>
			<small><small><?php echo \Joomla\CMS\Language\Text::sprintf('SSI_WITHDISC', $discount);?></small></small>
		<?php endif; ?>
	</button>
</form>

