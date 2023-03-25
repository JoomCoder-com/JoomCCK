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
<?php if(JFactory::getApplication()->input->getInt('width')):?>
<style>
<!--
body, body div {
	max-width:<?php echo JFactory::getApplication()->input->getInt('width');?>px !important;
    overflow-y: auto !important;
}
-->
</style>
<?php endif;?>

<?php 
echo $this->context;
?> 