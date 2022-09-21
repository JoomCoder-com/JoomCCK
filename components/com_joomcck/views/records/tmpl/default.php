<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access'); ?>
<?php global $app, $option, $Itemid;?>
<?php if (JFactory::getApplication()->input->getInt('modal', 0)):?>
<script type="text/javascript">
	window.parent.SqueezeBox.close();
</script>
<?php endif;?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<section id="joomcck-section-<?php echo $this->section->id ?>">
<?php echo $this->loadTemplate('markup_'.$this->section->params->get('general.tmpl_markup'));?>
</section>
