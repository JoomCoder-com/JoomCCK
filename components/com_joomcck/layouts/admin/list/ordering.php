<?php
/**
 * Compact Sort/Direction control for admin list views.
 *
 * Collapses the two legacy <select> elements into a single BS5 input-group
 * so it lives comfortably next to the secondary action buttons in the
 * card-header action bar.
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

$listOrder = $displayData->escape($displayData->state->get('list.ordering'));
$listDirn  = $displayData->escape($displayData->state->get('list.direction'));
?>

<div class="input-group input-group-sm cck-list-sort">
	<span class="input-group-text" id="cck-list-sort-label">
		<i class="fas fa-arrow-down-short-wide" aria-hidden="true"></i>
		<span class="ms-1"><?php echo Text::_('JGLOBAL_SORT_BY'); ?></span>
	</span>
	<select name="sortTable" id="sortTable" class="form-select"
	        onchange="Joomcck.orderTable('<?php echo $listOrder; ?>')"
	        aria-labelledby="cck-list-sort-label">
		<option value=""><?php echo Text::_('JGLOBAL_SORT_BY'); ?></option>
		<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $displayData->getSortFields(), 'value', 'text', $listOrder); ?>
	</select>
	<select name="directionTable" id="directionTable" class="form-select"
	        onchange="Joomcck.orderTable('<?php echo $listOrder; ?>')"
	        aria-label="<?php echo htmlspecialchars(Text::_('JFIELD_ORDERING_DESC'), ENT_QUOTES, 'UTF-8'); ?>"
	        style="max-width: 8.5rem;">
		<option value=""><?php echo Text::_('JFIELD_ORDERING_DESC'); ?></option>
		<option value="asc"  <?php if ($listDirn === 'asc')  echo 'selected="selected"'; ?>><?php echo Text::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
		<option value="desc" <?php if ($listDirn === 'desc') echo 'selected="selected"'; ?>><?php echo Text::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
	</select>
</div>
