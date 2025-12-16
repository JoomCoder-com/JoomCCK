<?php
/**
 * by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

$total = $this->statistic->get('new', 0) + $this->statistic->get('old', 0) + $this->statistic->get('skipped', 0);
$hasErrors = $this->statistic->get('errors', 0) > 0;
?>

<ul class="nav nav-pills mb-4">
	<li class="nav-item"><a class="nav-link" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&step=1&section_id='.$this->input->get('section_id')); ?>">1. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOAD')?></a></li>
	<li class="nav-item"><a class="nav-link">2. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTCONFIG')?></a></li>
	<li class="nav-item"><a class="nav-link">3. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTPREVIEW')?></a></li>
	<li class="nav-item"><a class="nav-link active">4. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTFINISH')?></a></li>
</ul>

<?php if($total > 0): ?>
<div class="alert alert-success">
    <i class="fa fa-check-circle me-2"></i><?php echo \Joomla\CMS\Language\Text::_('CSUCCESIMPORT'); ?>
</div>
<?php else: ?>
<div class="alert alert-warning">
    <i class="fa fa-exclamation-triangle me-2"></i><?php echo \Joomla\CMS\Language\Text::_('CIMPORTNOTHINGIMPORTED'); ?>
</div>
<?php endif; ?>

<div class="card mb-4">
	<div class="card-header">
		<strong><i class="fa fa-chart-bar me-2"></i><?php echo \Joomla\CMS\Language\Text::_('CIMPORTSTATS'); ?></strong>
	</div>
	<div class="card-body">
		<div class="row text-center">
			<div class="col-md-3 col-6 mb-3">
				<div class="border rounded p-3">
					<div class="display-6 fw-bold text-primary"><?php echo $total; ?></div>
					<div class="text-muted small"><?php echo \Joomla\CMS\Language\Text::_('CMPORTTOTAL'); ?></div>
				</div>
			</div>
			<div class="col-md-3 col-6 mb-3">
				<div class="border rounded p-3">
					<div class="display-6 fw-bold text-success"><?php echo $this->statistic->get('new', 0); ?></div>
					<div class="text-muted small"><?php echo \Joomla\CMS\Language\Text::_('CMPORTNEW'); ?></div>
				</div>
			</div>
			<div class="col-md-3 col-6 mb-3">
				<div class="border rounded p-3">
					<div class="display-6 fw-bold text-info"><?php echo $this->statistic->get('old', 0); ?></div>
					<div class="text-muted small"><?php echo \Joomla\CMS\Language\Text::_('CMPORTOLD'); ?></div>
				</div>
			</div>
			<div class="col-md-3 col-6 mb-3">
				<div class="border rounded p-3">
					<div class="display-6 fw-bold text-warning"><?php echo $this->statistic->get('skipped', 0); ?></div>
					<div class="text-muted small"><?php echo \Joomla\CMS\Language\Text::_('CMPORTSKIPPED'); ?></div>
				</div>
			</div>
		</div>

		<?php if($this->statistic->get('preset_name')): ?>
		<hr>
		<div class="row">
			<div class="col-md-6">
				<dl class="row mb-0">
					<dt class="col-sm-4"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTPRESET'); ?>:</dt>
					<dd class="col-sm-8"><?php echo htmlspecialchars($this->statistic->get('preset_name')); ?></dd>

					<?php if($this->statistic->get('import_method')): ?>
					<dt class="col-sm-4"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD'); ?>:</dt>
					<dd class="col-sm-8">
						<?php
						$method = $this->statistic->get('import_method', 'update');
						switch($method) {
							case 'skip': echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD_SKIP'); break;
							case 'duplicate': echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD_DUPLICATE'); break;
							default: echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD_UPDATE');
						}
						?>
					</dd>
					<?php endif; ?>
				</dl>
			</div>
			<div class="col-md-6">
				<?php if($this->statistic->get('duration')): ?>
				<dl class="row mb-0">
					<dt class="col-sm-4"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTDURATION'); ?>:</dt>
					<dd class="col-sm-8"><?php echo $this->statistic->get('duration'); ?> <?php echo \Joomla\CMS\Language\Text::_('CSECONDS'); ?></dd>
				</dl>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>

<div class="d-flex justify-content-between">
	<a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&step=1&section_id='.$this->input->get('section_id')); ?>" class="btn btn-primary">
		<i class="fa fa-plus me-1"></i><?php echo \Joomla\CMS\Language\Text::_('CIMPORTNEW'); ?>
	</a>
	<?php if($this->section): ?>
	<a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=records&section_id='.$this->input->get('section_id')); ?>" class="btn btn-outline-secondary">
		<?php echo \Joomla\CMS\Language\Text::_('CVIEWRECORDS'); ?> <i class="fa fa-arrow-right ms-1"></i>
	</a>
	<?php endif; ?>
</div>
