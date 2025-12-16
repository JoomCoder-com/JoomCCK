<?php
/**
 * by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<ul class="nav nav-pills mb-4">
	<li class="nav-item"><a class="nav-link" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&step=1&section_id='.$this->input->get('section_id')); ?>">1. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOAD')?></a></li>
	<li class="nav-item"><a class="nav-link" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&step=2&section_id='.$this->input->get('section_id').'&type_id='.$this->input->get('type_id')); ?>">2. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTCONFIG')?></a></li>
	<li class="nav-item"><a class="nav-link active">3. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTPREVIEW')?></a></li>
	<li class="nav-item"><a class="nav-link">4. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTFINISH')?></a></li>
</ul>

<div class="alert alert-info">
	<i class="fa fa-info-circle me-2"></i><?php echo \Joomla\CMS\Language\Text::_('CIMPORTPREVIEW_DESC'); ?>
</div>

<div class="card mb-4">
	<div class="card-header">
		<strong><?php echo \Joomla\CMS\Language\Text::_('CIMPORTPREVIEW_SETTINGS'); ?></strong>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-6">
				<dl class="row mb-0">
					<dt class="col-sm-4"><?php echo \Joomla\CMS\Language\Text::_('CNAME'); ?>:</dt>
					<dd class="col-sm-8"><?php echo htmlspecialchars($this->preset->params->get('name', '')); ?></dd>

					<dt class="col-sm-4"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD'); ?>:</dt>
					<dd class="col-sm-8">
						<?php
						$method = $this->preset->params->get('method', 'update');
						switch($method) {
							case 'skip': echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD_SKIP'); break;
							case 'duplicate': echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD_DUPLICATE'); break;
							default: echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD_UPDATE');
						}
						?>
					</dd>

					<dt class="col-sm-4"><?php echo \Joomla\CMS\Language\Text::_('CMPORTTOTAL'); ?>:</dt>
					<dd class="col-sm-8"><?php echo $this->total_rows; ?></dd>
				</dl>
			</div>
			<div class="col-md-6">
				<dl class="row mb-0">
					<dt class="col-sm-4"><?php echo \Joomla\CMS\Language\Text::_('ID'); ?>:</dt>
					<dd class="col-sm-8"><code><?php echo htmlspecialchars($this->preset->params->get('field.id', '-')); ?></code></dd>

					<?php if($this->preset->params->get('field.title')): ?>
					<dt class="col-sm-4"><?php echo \Joomla\CMS\Language\Text::_('CTITLE'); ?>:</dt>
					<dd class="col-sm-8"><code><?php echo htmlspecialchars($this->preset->params->get('field.title', '-')); ?></code></dd>
					<?php endif; ?>

					<?php if($this->preset->params->get('field.category')): ?>
					<dt class="col-sm-4"><?php echo \Joomla\CMS\Language\Text::_('CCATEGORY'); ?>:</dt>
					<dd class="col-sm-8"><code><?php echo htmlspecialchars($this->preset->params->get('field.category', '-')); ?></code></dd>
					<?php endif; ?>
				</dl>
			</div>
		</div>
	</div>
</div>

<div class="card mb-4">
	<div class="card-header">
		<strong><?php echo \Joomla\CMS\Language\Text::sprintf('CIMPORTPREVIEW_SAMPLE', min(5, $this->total_rows)); ?></strong>
	</div>
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-bordered table-striped table-sm mb-0">
				<thead class="table-light">
					<tr>
						<th style="width: 50px;">#</th>
						<th><?php echo \Joomla\CMS\Language\Text::_('ID'); ?></th>
						<?php if($this->type->params->get('properties.item_title') == 1): ?>
						<th><?php echo \Joomla\CMS\Language\Text::_('CTITLE'); ?></th>
						<?php endif; ?>
						<?php if($this->preset->params->get('field.category')): ?>
						<th><?php echo \Joomla\CMS\Language\Text::_('CCATEGORY'); ?></th>
						<?php endif; ?>
						<?php foreach($this->mapped_fields as $field): ?>
						<th><?php echo htmlspecialchars($field->label); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach($this->preview_rows as $i => $row): ?>
					<tr>
						<td class="text-muted"><?php echo $i + 1; ?></td>
						<td>
							<?php
							$id_field = $this->preset->params->get('field.id');
							echo htmlspecialchars($row->get($id_field, '-'));
							?>
						</td>
						<?php if($this->type->params->get('properties.item_title') == 1): ?>
						<td>
							<?php
							$title_field = $this->preset->params->get('field.title');
							$title = $row->get($title_field, '');
							echo htmlspecialchars(mb_substr($title, 0, 50)) . (mb_strlen($title) > 50 ? '...' : '');
							?>
						</td>
						<?php endif; ?>
						<?php if($this->preset->params->get('field.category')): ?>
						<td>
							<?php
							$cat_field = $this->preset->params->get('field.category');
							echo htmlspecialchars($row->get($cat_field, '-'));
							?>
						</td>
						<?php endif; ?>
						<?php foreach($this->mapped_fields as $field): ?>
						<td>
							<?php
							$field_col = $this->preset->params->get('field.' . $field->id . '.fname', '');
							$value = $field_col ? $row->get($field_col, '') : '';
							if(is_string($value)) {
								echo htmlspecialchars(mb_substr($value, 0, 50)) . (mb_strlen($value) > 50 ? '...' : '');
							} else {
								echo '-';
							}
							?>
						</td>
						<?php endforeach; ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm">
	<div class="d-flex justify-content-between">
		<a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&step=2&section_id='.$this->input->get('section_id').'&type_id='.$this->input->get('type_id')); ?>" class="btn btn-secondary">
			<i class="fa fa-arrow-left me-1"></i><?php echo \Joomla\CMS\Language\Text::_('CPREV'); ?>
		</a>
		<button type="submit" class="btn btn-success">
			<i class="fa fa-check me-1"></i><?php echo \Joomla\CMS\Language\Text::_('CIMPORTSTART'); ?>
		</button>
	</div>
	<input type="hidden" name="task" value="import.import">
	<input type="hidden" name="step" value="4">
	<input type="hidden" name="preset" value="<?php echo $this->preset->id; ?>">
	<input type="hidden" name="type_id" value="<?php echo $this->input->get('type_id'); ?>">
	<input type="hidden" name="section_id" value="<?php echo $this->input->get('section_id'); ?>">
</form>
