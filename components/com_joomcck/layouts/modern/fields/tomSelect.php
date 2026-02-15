<?php
/**
 * Joomcck by joomcoder
 * Modern UI - TomSelect Layout
 *
 * Tailwind CSS styled TomSelect multi-select dropdown.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;

defined('_JEXEC') or die();

extract($displayData);

$wa = Webassets::$wa;

$wa->useScript('com_joomcck.tom-select');
$wa->useStyle('com_joomcck.tom-select');

// fields names
$valueFieldName = isset($options['valueFieldName']) && $options['valueFieldName'] ? $options['valueFieldName'] : 'id';
$labelFieldName = isset($options['labelFieldName']) && $options['labelFieldName'] ? $options['labelFieldName'] : 'text';
$searchFieldName = isset($options['searchFieldName']) && $options['searchFieldName'] ? $options['searchFieldName'] : 'text';

// transform to js json format
$list = json_encode($list);
$list = str_replace(['"'.$valueFieldName.'":','"'.$labelFieldName.'":'],[''.$valueFieldName.':',''.$labelFieldName.':'],$list);

if(empty($default) && !empty($list)){
	$default = $list;
}else{
	$default = json_encode($default);
	$default = str_replace(['"id":','"'.$labelFieldName.'":'],['id:',''.$labelFieldName.':'],$default);
}

$fieldId = (int) rand(1,2000);

?>
<div id="select-items-<?php echo $id ?>-container">
	<select
		id="<?php echo $id ?>-<?php echo $fieldId ?>"
		name="<?php echo $name ?>[]"
		multiple
		data-placeholder="<?php echo \Joomla\CMS\Language\Text::_('CTYPETOSELECT') ?>"
	>
	</select>
</div>
<script>

	let tomSelected<?php echo $fieldId ?> = new TomSelect("#<?php echo $id ?>-<?php echo $fieldId ?>",{
		plugins: <?php echo $options['canDelete'] ? "['remove_button']" : "[]"; ?>,
		create: <?php echo $options['canAdd'] ?>,
		valueField: '<?php echo $valueFieldName ?>',
		labelField: '<?php echo $labelFieldName ?>',
		searchField : '<?php echo $searchFieldName ?>',
		options: <?php echo $default ?>,
		items: <?php echo $list ?>,
		maxItems: <?php echo $options['maxItems'] ?>,
		maxOptions: <?php echo $options['maxOptions'] ?>,
		<?php if(!empty($options['suggestion_url'])): ?>
		load: function(query, callback) {
			var url = '<?php echo \Joomla\CMS\Uri\Uri::root().$options['suggestion_url'] ?>';
			fetch(url)
				.then(response => response.json())
				.then(json => {
					callback(json.result);
				}).catch(()=>{
				callback();
			});
		}
		<?php endif; ?>

	});

	// Apply Tailwind styling instead of Bootstrap
	(function() {
		var container = document.querySelector('#select-items-<?php echo $id ?>-container .ts-control');
		if (container) {
			container.classList.add('w-full', 'border', 'border-gray-300', 'rounded', 'px-3', 'py-2', 'text-sm');
			container.classList.add('focus-within:ring-1', 'focus-within:ring-primary', 'focus-within:border-primary');
			var input = container.querySelector('input');
			if (input) input.classList.add('w-full');
		}
	})();

	// on add new item, select new item
	<?php if(!empty($options['onAdd'])): ?>
	tomSelected<?php echo $fieldId ?>.on('item_add',function(value,data){

		const selectedOption = this.getOption(value);
		let id = null;
		let text = '';

		if($.isNumeric(value)){
			text = $(selectedOption).text();
			id = value;
		}else{
			let optionsList =  Object.entries(this.options);
			let itemsNumber = optionsList.length;
			let itemsBreak = 1;

			optionsList.forEach(([key, option]) => {
				if(itemsBreak == itemsNumber)
					return;
				if(option.text == value){
					this.removeOption(value);
					return false;
				}
				itemsBreak++;
			});

			text = value;
		}

		$.ajax({
			dataType: 'json',
			type: 'get', async: false,
			url: '<?php echo \Joomla\CMS\Uri\Uri::root().$options['onAdd'] ?>',
			data: {tid: id, text: text}
		}).done(function(json) {
			console.log(json);
		});

	});
	<?php endif; ?>

	// remove ajax tag
	<?php if(!empty($options['onRemove'])): ?>
	tomSelected<?php echo $fieldId ?>.on('item_remove',function(value,item){

		$.ajax({
			dataType: 'json',
			type: 'get', async: false,
			url: '<?php echo \Joomla\CMS\Uri\Uri::root().$options['onRemove'] ?>',
			data: {tid: value}
		}).done(function(json) {
		});

	});
	<?php endif; ?>

</script>
