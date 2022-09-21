<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

?>

<input type="password" name="jform[fields][<?php echo $this->id; ?>]" value="<?php echo $this->encrypt; ?>" autocomplete="off"/>

<!-- Hack for Chrome autocomplete. -->
<div style="display: none;">
	<input type="text" id="PreventChromeAutocomplete" name="PreventChromeAutocomplete" autocomplete="address-level4"/>
</div>