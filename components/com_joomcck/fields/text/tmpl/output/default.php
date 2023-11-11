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

<?php echo $this->params->get('params.prepend');?>

<?php if($this->params->get('params.qr_code', 0)) : 
    $width = $this->params->get('params.qr_width', 60); ?>
	<img src="http://chart.apis.google.com/chart?chs=<?php echo $width;?>x<?php echo $width;?>&cht=qr&chld=L|0&chl=<?php echo urlencode(strip_tags($this->value));?>" 
			title="<?php echo \Joomla\CMS\Language\Text::_('TXT_QR');?>" class="qr-image" width="<?php echo $width;?>" height="<?php echo $width;?>" align="absmiddle">
<?php endif; ?>

<?php echo $this->value;?>
			
<?php if($this->readmore) : ?>
	<p><?php echo $this->readmore;?></p>
<?php endif; ?>

<?php echo $this->params->get('params.append');?>