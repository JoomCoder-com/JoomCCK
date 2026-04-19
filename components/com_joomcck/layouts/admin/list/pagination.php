<?php
/**
 * Compact card-footer pagination for admin list views.
 *
 * Lays links on the left, meta (page + result counter) in the middle, and
 * the limit box on the right. Wraps gracefully on small viewports.
 */

defined('_JEXEC') or die('Restricted access');

extract($displayData);
?>

<div class="cck-list-footer">
	<?php if ($pagination->getPagesLinks()): ?>
		<div class="joomcckPageLinks">
			<?php echo $pagination->getPagesLinks(); ?>
		</div>
	<?php else: ?>
		<div class="joomcckPageLinks"></div>
	<?php endif; ?>

	<div class="cck-list-meta">
		<?php if ($pagination->getPagesCounter()): ?>
			<span class="cck-list-page-counter"><?php echo $pagination->getPagesCounter(); ?></span>
			<span class="bullet" aria-hidden="true">&middot;</span>
		<?php endif; ?>
		<span class="cck-list-results"><?php echo $pagination->getResultsCounter(); ?></span>
	</div>

	<div class="joomcckLimitBox">
		<?php echo $pagination->getLimitBox(); ?>
	</div>
</div>
