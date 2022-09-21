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

<input autocomplete="off" id="flt<?php echo $this->id;?>" type="text" name="filters[<?php echo $this->key;?>]"
	data-autocompleter-default="<?php echo $this->value;?>" value="<?php echo $this->value;?>">

<script type="text/javascript">
var labels, mapped;

jQuery('#flt<?php echo $this->id;?>').typeahead({
	items: 10,
	source: function (query, process) {
		return jQuery.get(Joomcck.field_call_url,
		{
			field_id: <?php echo $this->id ?>,
			func:'onFilterData',
			field:'url',
			q: query
		},

		function (data) {
			if(!data)
			{
				return;
			}

			if(!data.result)
			{
				return;
			}

			labels = []
			mapped = {}

			jQuery.each(data.result, function (i, item) {
				mapped[item.label] = item.value
				labels.push(item.label)
			});

			return process(labels);
		},
		'json'
		);
	},
	updater: function (item) {
		return mapped[item];
	}
});

</script>