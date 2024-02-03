<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
$key = $vars->index . '_' . $vars->rid;

?>
<style>
	#<?php echo $vars->rating_ident; ?>_rateBox span {
		background: url('<?php  echo $vars->img_path ?>flag-black.png') no-repeat;
		width: 22px;
		height: 22px;
		display: inline-block;
	}

	#<?php echo $vars->rating_ident; ?>_rateBox span.on {
		background: url('<?php  echo $vars->img_path ?>flag.png') no-repeat;
		display: inline-block;
	}
</style>

<div id="<?php echo $vars->rating_ident; ?>_rateBox"></div>

<script type="text/javascript">
	var newRating<?php echo $key;?> = felixRating.newRating('<?php echo $vars->rating_ident; ?>_rateBox', <?php echo $vars->rating_active; ?>);
	newRating<?php echo $key;?>.setStars({ 
		"20": '<?php echo JText::_("20%")?>',
		"40": '<?php echo JText::_("40%")?>',
		"60": '<?php echo JText::_("60%")?>',
		"80": '<?php echo JText::_("80%")?>',
		"100": '<?php echo JText::_("100%")?>'
		});
	newRating<?php echo $key;?>.setCurrentStar("<?php echo $vars->rating_current; ?>");
	newRating<?php echo $key;?>.setIndex(<?php echo $vars->index?>);
	<?php if( $vars->callbackfunction ){ ?>
	newRating<?php echo $key;?>.setSedingFunction(<?php echo $vars->callbackfunction; ?>, '<?php echo $vars->prod_id; ?>');
	<?php } ?>
</script>