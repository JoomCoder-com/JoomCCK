<?php
/**
 * Joomcck by joomcoder
 * Modern UI - List Group Layout
 *
 * Tailwind CSS replacement for Bootstrap list-group.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

?>

<ul class="divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden">
	<?php foreach ($items as $item): ?>
		<li class="px-4 py-2.5 text-sm text-gray-700 bg-white hover:bg-gray-50"><?php echo $item['text'] ?></li>
	<?php endforeach; ?>
</ul>
