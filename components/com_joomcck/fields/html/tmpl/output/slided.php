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

<div id="shorttext<?php echo $this->id . "-" . $record->id?>">
	<?php echo $this->value_striped?>
</div>
<div id="hiddentext<?php echo $this->id . "-" . $record->id?>" style="display: none;">
	<?php echo $this->value?>
</div>
<a href="javascript:void(0);"
	onclick="jQuery( '#shorttext<?php echo $this->id . "-" . $record->id?>, #hiddentext<?php echo $this->id . "-" . $record->id?>' ).slideToggle();"><?php echo JText::_($this->params->get('params.readmore_lbl','H_READMORE'));?></a>
