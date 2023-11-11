<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

\Joomla\CMS\Factory::getDocument()->addScript(JURI::root(TRUE) . '/media/com_joomcck/vendors/jquery.countdown/dist/jquery.countdown.min.js');

$val = $this->value[0];
$date = '<span class="countdown_date">'.$this->dates[0].'</span>';
$text = (strtotime($val) > time()) ? \Joomla\CMS\Language\Text::sprintf('D_LEFT', $date) : \Joomla\CMS\Language\Text::sprintf('D_PAST', $date);

$format = $this->params->get('tmpl_countdown.cd_format', '%d days %H hr %M min %S sec');
if((int)$format == 100) {
	$format = $this->params->get('tmpl_countdown.cd_custom', '%d days %H hr %M min %S sec');
}
$format = preg_replace("/(%[a-zA-Z]{1})/", "<span>$1</span>", $format);
?>

<style>
	#clock<?php echo $this->id ?>{ font-size: 16px; font-weight: bold; }
	#clock<?php echo $this->id ?> span { font-size: 26px; font-weight: bold; }
</style>

<p class="countdown_msg"><?php echo $text;?>: </p>
<div id="clock<?php echo $this->id ?>"></div>


<script type='text/javascript'>
(function($){
<?php if($this->params->get('tmpl_countdown.elapse')): ?>
	$('#clock<?php echo $this->id ?>')
	.countdown('<?php echo $val ?>', {elapse: true})
	.on('update.countdown', function(event) {
		$(this).html(event.strftime('<?php echo $format ?>'));
	});
<?php else: ?>
	$('#clock<?php echo $this->id ?>').countdown('<?php echo $val ?>', function(event) {
		var $this = $(this).html(event.strftime('<?php echo $format ?>'));
	});
<?php endif; ?>
}(jQuery))
</script>
