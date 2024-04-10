

<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

HTMLHelper::_('bootstrap.tab');

// some inits
$k = 0;

?>
<div id="joomcck-submission-form" class="jcck-form-verticaltabs">
	<div class="d-flex align-items-start">
		<ul class="nav nav-pills flex-column p-2 align-items-end sticky-top" id="joomcckformTab-tab" role="tablist">
			<li class="nav-item w-100 " role="presentation">
				<button class="w-100 nav-link active position-relative text-end" id="main-tab-item" data-bs-toggle="pill" data-bs-target="#main-tab" type="button" role="tab" aria-controls="main-tab-item" aria-selected="true">
					<?php echo Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')) ?>
				</button>
			</li>
			<?php if (isset($current->sorted_fields)): ?>
				<?php foreach ($current->sorted_fields as $group_id => $fields) : ?>
					<?php if ($group_id == 0) continue; ?>
					<li class="nav-item w-100" role="presentation">
						<button class="w-100 nav-link position-relative text-end" id="tab-item-<?php echo $group_id ?>" data-bs-toggle="pill" data-bs-target="#tab-<?php echo $group_id ?>" type="button" role="tab" aria-controls="tab-item-<?php echo $group_id ?>" aria-selected="true">
							<?php echo HTMLFormatHelper::icon($current->field_groups[$group_id]['icon']) ?><?php echo $current->field_groups[$group_id]['name'] ?>
						</button>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if (count($current->meta)): ?>
			<li class="nav-item w-100" role="presentation">
				<button class="w-100 nav-link position-relative  text-end" id="meta-tab-item" data-bs-toggle="pill" data-bs-target="#meta-tab" type="button" role="tab" aria-controls="meta-tab-item" aria-selected="true">
					<?php echo Text::_('CMETADATA') ?>
				</button>
			</li>
			<?php endif; ?>

			<?php if (count($current->core_admin_fields)): ?>
			<li class="nav-item w-100" role="presentation">
				<button class="w-100 nav-link position-relative  text-end" id="admin-tab-item" data-bs-toggle="pill" data-bs-target="#admin-tab" type="button" role="tab" aria-controls="admin-tab-item" aria-selected="true">
					<?php echo Text::_('CSPECIALFIELD') ?>
				</button>
			</li>
			<?php endif; ?>

			<?php if (count($current->core_fields)): ?>
			<li class="nav-item w-100" role="presentation">
				<button class="w-100 nav-link position-relative" id="core-tab-item" data-bs-toggle="pill" data-bs-target="#core-tab" type="button" role="tab" aria-controls="core-tab-item" aria-selected="true">
					<?php echo Text::_('CCOREFIELDS') ?>
				</button>
			</li>
			<?php endif; ?>

		</ul>
		<div class="tab-content p-3 flex-grow-1 border-start" id="joomcckformTab-tabContent">
			<div class="tab-pane fade show active" id="main-tab" role="tabpanel" aria-labelledby="main-tab">
				<?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]) ?>
			</div>

			<?php if (isset($current->sorted_fields)): // grouped fields ?>
				<?php foreach ($current->sorted_fields as $group_id => $fields) : ?>
					<div class="tab-pane fade" id="tab-<?php echo $group_id ?>" role="tabpanel" aria-labelledby="tab-<?php echo $group_id ?>">
						<?php if (!empty($current->field_groups[$group_id]['descr'])): ?>
							<?php echo $current->field_groups[$group_id]['descr']; ?>
						<?php endif; ?>
						<?php foreach ($fields as $field_id => $field): ?>
							<?php echo Layout::render('core.submission.formFields.field', ['current' => $current,'k' => $k, 'field' => $field]) // field part ?>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>


			<?php if (count($current->meta)): ?>
				<div class="tab-pane fade" id="meta-tab" role="tabpanel" aria-labelledby="meta-tab">
					<?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]) // metadata field part ?>
				</div>
			<?php endif; ?>

			<?php if (count($current->core_admin_fields)): ?>
				<div class="tab-pane fade" id="admin-tab" role="tabpanel" aria-labelledby="admin-tab">
					<?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]) // admin field part ?>
				</div>
			<?php endif; ?>


			<?php if (count($current->core_fields)): ?>
				<div class="tab-pane fade" id="core-tab" role="tabpanel" aria-labelledby="core-tab">
					<?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]) // core field part ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</div>