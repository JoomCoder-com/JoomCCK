<?php
/**
 * Joomcck by joomcoder
 * Modern UI - TomSelect Tags Ajax Layout
 *
 * Tailwind CSS styled TomSelect tag input with AJAX.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

$wa = Webassets::$wa;

$wa->useScript('com_joomcck.tom-select');
$wa->useStyle('com_joomcck.tom-select');

$cantAdd = $options['can_add'] ? 'true' : 'false';

$selected = json_encode($selected);
$default = str_replace(['"id":','"text":'],['id:','text:'],json_encode($default));

?>

<div id="select-tags-<?php echo $id ?>-container">
	<select
		id="<?php echo $id ?>"
		name="<?php echo $name ?>[]"
		multiple
		data-placeholder="<?php echo Text::_('CADDTAGS') ?>"
	>
	</select>
</div>

<script>

	new TomSelect("#<?php echo $id ?>",{
		plugins: ['remove_button','clear_button'],
		maxItems: <?php echo $options['max_items'] ?>,
		maxOptions: <?php echo $options['suggestion_limit'] ?>,
		create: <?php echo $cantAdd ?>,
		options: <?php echo $default ?>,
		items: <?php echo $selected ?>,
		valueField: 'id',
		labelField: 'text',
		searchField : 'text',
		load: function(query, callback) {
			var url = '<?php echo $options['suggestion_url'] ?>';
			fetch(url)
				.then(response => response.json())
				.then(json => {
					callback(json.result);
				}).catch(()=>{
				callback();
			});
		},
	});

	// Apply Tailwind styling instead of Bootstrap
	(function() {
		var container = document.querySelector('#select-tags-<?php echo $id ?>-container .ts-control');
		if (container) {
			container.classList.add('w-full', 'border', 'border-gray-300', 'rounded', 'px-3', 'py-2', 'text-sm');
			container.classList.add('focus-within:ring-1', 'focus-within:ring-primary', 'focus-within:border-primary');
			var input = container.querySelector('input');
			if (input) input.classList.add('w-full');
		}
	})();

</script>
