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

<div class="input text" id="flt_div<?php echo $this->id;?>">
<input class="form-control" autocomplete="off" id="flt<?php echo $module.$this->id;?>" type="text" name="filters[<?php echo $this->key;?>]"
	data-autocompleter-default="<?php echo $this->value;?>"
	value="<?php echo $this->value;?>"></div>

<script type="text/javascript">
Joomcck.typeahead('#flt<?php echo $module.$this->id;?>', {
	field_id: <?php echo $this->id ?>,
	func:'onFilterData',
}, {limit:12});
</script>