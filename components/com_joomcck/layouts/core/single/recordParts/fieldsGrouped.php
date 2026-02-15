<?php
/**
 * Joomcck by joomcoder
 * Core Layout - Grouped Fields (tabs/accordions/fieldsets)
 *
 * Extracted from default_record_default.php including helper functions.
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

extract($displayData);

$params = $current->tmpl_params['record'];
$item = $current->item;
$started = false;
$i = $o = 0;

// Helper functions
if (!function_exists('_jcck_group_start')) {
	function _jcck_group_start($data, $label, $name, $j = 0) {
		static $start = false;
		$icon = '';
		if (!empty($data->item->field_groups[$label]['icon']) && $data->tmpl_params['record']->get('tmpl_params.show_groupicon', 1)) {
			$icon = HTMLFormatHelper::icon($data->item->field_groups[$label]['icon']);
		}
		switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0)) {
			case 1: // tab
				if (!$start) {
					echo '<div class="tab-content" id="tabs-box">';
					$start = true;
				}
				echo $j == 0 ? '<div class="tab-pane show active" id="' . $name . '">' : '<div class="tab-pane" id="' . $name . '">';
				break;
			case 2: // accordion
				HTMLHelper::_('bootstrap.collapse');
				if (!$start) {
					echo '<div class="accordion" id="accordion2">';
					$start = true;
				}
				echo '<div class="accordion-group"><div class="accordion-heading"><a class="accordion-toggle" data-bs-toggle="collapse" data-bs-parent="#accordion2" href="#' . $name . '">' . $icon . ' ' . $label . '</a></div><div id="' . $name . '" class="accordion-body collapse"><div class="accordion-inner">';
				break;
			case 3: // fieldset
				echo "<legend>{$icon} {$label}</legend>";
				break;
		}
		if ($data->tmpl_params['record']->get('tmpl_params.show_groupdescr') && !empty($data->item->field_groups[$label]['descr'])) {
			echo $data->item->field_groups[$label]['descr'];
		}
	}

	function _jcck_group_end($data) {
		switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0)) {
			case 1: echo '</div>'; break;
			case 2: echo '</div></div></div>'; break;
		}
	}

	function _jcck_total_end($data) {
		switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0)) {
			case 1: case 2: echo '</div>'; break;
		}
	}
}

?>

<?php if (in_array($params->get('tmpl_params.item_grouping_type', 0), array(1)) && count($current->item->fields_by_groups)): ?>
	<div class="clearfix"></div>
	<div class="tabbable <?php echo $params->get('tmpl_params.tabs_position'); ?>">
		<ul class="nav <?php echo $params->get('tmpl_params.tabs_style', 'nav-tabs'); ?>" id="tabs-list">
			<?php if (isset($current->item->fields_by_groups)): ?>
				<?php $f = 0; ?>
				<?php foreach ($current->item->fields_by_groups as $group_id => $fields): ?>
					<?php $active = ($f == 0) ? 'active' : ''; ?>
					<li class="nav-item">
						<a class="nav-link <?php echo $active ?>" href="#tab-<?php echo $o++ ?>" data-bs-toggle="tab">
							<?php if (!empty($item->field_groups[$group_id]['icon']) && $params->get('tmpl_params.show_groupicon', 1)): ?>
								<?php echo HTMLFormatHelper::icon($item->field_groups[$group_id]['icon']) ?>
							<?php endif; ?>
							<?php echo \Joomla\CMS\Language\Text::_($group_id) ?>
						</a>
					</li>
					<?php $f++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
<?php endif; ?>

<?php if (isset($current->item->fields_by_groups)): ?>
	<?php $j = 0; ?>
	<?php foreach ($current->item->fields_by_groups as $group_name => $fields): ?>
		<?php $started = true; ?>
		<?php _jcck_group_start($current, $group_name, 'tab-' . $i++, $j); ?>
		<dl class="dl-horizontal fields-list fields-group<?php echo $i; ?>">
			<?php foreach ($fields as $field_id => $field): ?>
				<dt id="<?php echo 'dt-' . $field_id; ?>" class="<?php echo $field->class; ?>">
					<?php if ($field->params->get('core.show_lable') > 1): ?>
						<label id="<?php echo $field->id; ?>-lbl">
							<?php if (!$field->params->get('core.label_icon_type', 0) && !empty($field->params->get('core.icon', ''))): ?>
								<?php echo HTMLFormatHelper::icon($field->params->get('core.icon')); ?>
							<?php elseif (!empty($field->params->get('core.label_icon_class', ''))): ?>
								<i class="<?php echo $field->params->get('core.label_icon_class') ?>"></i>
							<?php endif; ?><?php echo $field->label; ?>
						</label>
					<?php endif; ?>
				</dt>
				<dd id="<?php echo 'dd-' . $field_id; ?>" class="<?php echo $field->fieldclass; ?><?php echo ($field->params->get('core.label_break') > 1 ? ' line-brk' : NULL) ?>">
					<?php echo $field->result; ?>
				</dd>
			<?php endforeach; ?>
		</dl>
		<?php _jcck_group_end($current); ?>
		<?php $j++; ?>
	<?php endforeach; ?>
<?php endif; ?>

<?php if ($started): ?>
	<?php _jcck_total_end($current); ?>
<?php endif; ?>

<?php if (in_array($params->get('tmpl_params.item_grouping_type', 0), array(1)) && count($current->item->fields_by_groups)): ?>
	</div>
	<div class="clearfix"></div>
	<br />
<?php endif; ?>

<?php if ($started): ?>
	<script type="text/javascript">
		<?php if (in_array($params->get('tmpl_params.item_grouping_type', 0), array(2))): ?>
		jQuery('#tab-main').collapse('show');
		<?php endif; ?>
	</script>
<?php endif; ?>
