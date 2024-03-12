<?php
/**
 * JoomCCK Rating template by Joomla.ge
 * a component for Joomla! 5 CMS (http://www.joomla.org)
 * Author Website: http://joomla.ge/
 * @copyright Copyright (C) 2012 MintJoomla (http://joomla.ge). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
$key = $vars->index . '_' . $vars->rid;

?>
<style>
	#<?php echo $vars->rating_ident; ?>_rateBox span {
		background: url('<?php  echo $vars->img_path ?>fancystar-gray.png') no-repeat;
		width: 22px;
		height: 20px;
		display: inline-block;
	}

	#<?php echo $vars->rating_ident; ?>_rateBox span.on {
		background: url('<?php  echo $vars->img_path ?>fancystar.png') no-repeat;
		display: inline-block;
	}
</style>

<div id="<?php echo $vars->rating_ident; ?>_rateBox"></div>

<script type="text/javascript">
	var newRating<?php echo $key;?> = felixRating.newRating('<?php echo $vars->rating_ident; ?>_rateBox', <?php echo $vars->rating_active; ?>);
	newRating<?php echo $key;?>.setStars({ 
		"10": '<?php echo \Joomla\CMS\Language\Text::_("1")?>',
		"20": '<?php echo \Joomla\CMS\Language\Text::_("2")?>',
		"30": '<?php echo \Joomla\CMS\Language\Text::_("3")?>',
		"40": '<?php echo \Joomla\CMS\Language\Text::_("4")?>',
		"50": '<?php echo \Joomla\CMS\Language\Text::_("5")?>',
		"60": '<?php echo \Joomla\CMS\Language\Text::_("6")?>',
		"70": '<?php echo \Joomla\CMS\Language\Text::_("7")?>',
		"80": '<?php echo \Joomla\CMS\Language\Text::_("8")?>',
		"90": '<?php echo \Joomla\CMS\Language\Text::_("9")?>',
		"100": '<?php echo \Joomla\CMS\Language\Text::_("10")?>'
		});
	newRating<?php echo $key;?>.setCurrentStar("<?php echo $vars->rating_current; ?>");
	newRating<?php echo $key;?>.setIndex(<?php echo $vars->index?>);
	<?php if( $vars->callbackfunction ){ ?>
	newRating<?php echo $key;?>.setSedingFunction(<?php echo $vars->callbackfunction; ?>, '<?php echo $vars->prod_id; ?>');
	<?php } ?>
</script>