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

<div class="page-header">
	<h1>
		<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/import.png">
		<?php echo JText::_('CIMPORT'); ?>
	</h1>
</div>

<?php 
echo $this->loadTemplate('step'.JFactory::getApplication()->input->get('step', 1));
?>
