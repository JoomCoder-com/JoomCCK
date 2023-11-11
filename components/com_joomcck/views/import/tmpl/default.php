<?php
/**
 * by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<div class="page-header mb-3">
	<h1>
		<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE); ?>/components/com_joomcck/images/icons/import.png">
		<?php echo \Joomla\CMS\Language\Text::_('CIMPORT'); ?>
	</h1>
</div>

<?php 
echo $this->loadTemplate('step'.\Joomla\CMS\Factory::getApplication()->input->get('step', 1));
?>
