<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>

<?php if($this->value == -1 || $this->value == NULL): ?>
	<p>Article is free</p>
	<?php return; endif; ?>

<?php if($this->value == 1): ?>
	<?php if($this->paid): ?>
		<p>You can read this</p>
		<?php else:?>
		<p>You cannot read this</p>
	<?php endif; ?>
<?php endif; ?>
