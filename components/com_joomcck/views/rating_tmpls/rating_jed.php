<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
$key = $vars->index . '_' . $vars->rid;

?>
<style>
	#<?php echo $vars->rating_ident; ?>_rateBox span {
		background: url('<?php  echo $vars->img_path ?>jed-gray.png') no-repeat;
		width: 18px;
		height: 16px;
		display: inline-block;
	}

	#<?php echo $vars->rating_ident; ?>_rateBox span.on {
		background: url('<?php  echo $vars->img_path ?>jed.png') no-repeat;
		display: inline-block;
	}
</style>

<div id="<?php echo $vars->rating_ident; ?>_rateBox"></div>

<script type="text/javascript">
	var newRating<?php echo $key;?> = felixRating.newRating('<?php echo $vars->rating_ident; ?>_rateBox', <?php echo $vars->rating_active; ?>);
	newRating<?php echo $key;?>.setStars({ "20": '<?php echo JText::_("VOTE_NOTGOOD")?>',
		"40": '<?php echo JText::_("VOTE_FAIR")?>',
		"60": '<?php echo JText::_("VOTE_GOOD")?>',
		"80": '<?php echo JText::_("VOTE_VERYGOOD")?>',
		"100": '<?php echo JText::_("VOTE_EXEL")?>' });
	newRating<?php echo $key;?>.setCurrentStar("<?php echo $vars->rating_current; ?>");
	newRating<?php echo $key;?>.setIndex(<?php echo $vars->index?>);
	<?php if( $vars->callbackfunction ){ ?>
	newRating<?php echo $key;?>.setSedingFunction(<?php echo $vars->callbackfunction; ?>, '<?php echo $vars->prod_id; ?>');
	<?php } ?>
</script>