<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<?php echo $this->gateway_form;?>

<textarea class="form-control" name="jform[fields][<?php echo $this->id;?>][vouchers]" <?php echo $this->class;?> id="field_<?php echo $this->id;?>"><?php echo $this->text;?></textarea>
<p class="text-muted"><small><?php echo JText::_('PV_SEPARATEBYNL');?></small></p>