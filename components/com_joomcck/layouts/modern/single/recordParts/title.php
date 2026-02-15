<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Record Title Layout
 *
 * Tailwind CSS flex + badge replacement for Bootstrap page-header.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

$item   = $current->item;
$params = $current->tmpl_params['record'];

$containerClass = !isset($containerClass) ? 'mb-6 pb-2 border-b border-gray-200' : $containerClass;

?>
<?php if ($params->get('tmpl_core.item_title')): ?>
	<?php if ($current->type->params->get('properties.item_title')): ?>
	<div class="<?php echo $containerClass ?> flex items-center gap-3">
		<<?php echo $params->get('tmpl_params.title_tag', 'h1') ?> class="text-2xl font-bold text-gray-900 flex items-center gap-2">
			<span><?php echo $item->title ?></span>
			<?php if ($item->new): ?>
				<span class="jcck-badge jcck-badge-success text-xs"><?php echo Text::_('CNEW') ?></span>
			<?php endif; ?>
			<?php echo CEventsHelper::showNum('record', $item->id); ?>
		</<?php echo $params->get('tmpl_params.title_tag', 'h1') ?>>
	</div>
	<?php endif; ?>
<?php endif; ?>
