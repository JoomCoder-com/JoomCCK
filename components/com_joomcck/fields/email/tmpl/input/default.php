<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
		
$class = ' class="' . $this->params->get('core.field_class') . ($this->required ? ' required' : NULL) . '"';
$required = $this->required ? ' required="true" ' : NULL;
?>

<input type="text" name="jform[fields][<?php echo $this->id; ?>]" id="field_<?php echo $this->id; ?>" <?php echo $class.$required; ?> 
	value="<?php echo $this->value; ?>">
