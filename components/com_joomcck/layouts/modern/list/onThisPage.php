<?php
/**
 * Joomcck by joomcoder
 * Modern UI - On This Page Layout
 *
 * Tailwind CSS card replacement for Bootstrap card + list-group.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);
?>

<?php if($params->get('tmpl_core.show_title_index')):?>

	<div id="jcck-onthispage" class="jcck-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-4">
		<div class="jcck-card-header px-4 py-3 bg-gray-50 border-b border-gray-200">
			<h3 class="text-sm font-semibold text-gray-900"><?php echo \Joomla\CMS\Language\Text::_('CONTHISPAGE')?></h3>
		</div>
		<ul class="divide-y divide-gray-200">
			<?php foreach ($items AS $item):?>
				<li class="px-4 py-2">
					<a href="#record<?php echo $item->id?>" class="text-sm text-primary hover:underline"><?php echo $item->title?></a>
				</li>
			<?php endforeach;?>
		</ul>
	</div>
<?php endif;?>
