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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

?>

<?php if ($current->tmpl_params->get('tmpl_params.tab_main_descr')): // main fields post description ?>
	<?php echo $current->tmpl_params->get('tmpl_params.tab_main_descr'); ?>
<?php endif; ?>

<?php echo Layout::render('core.submission.formFields.title', ['current' => $current, 'k' => $k]) // title field part ?>

<?php echo Layout::render('core.submission.formFields.anyWhere', ['current' => $current, 'k' => $k]) // anywhere (who can report; where to repost) field part ?>

<?php echo Layout::render('core.submission.formFields.category', ['current' => $current, 'k' => $k]) // category field part ?>

<?php echo Layout::render('core.submission.formFields.userCategory', ['current' => $current, 'k' => $k]) // category field part ?>

<?php echo Layout::render('core.submission.formFields.multiRating', ['current' => $current, 'k' => $k]) // multirating field part ?>


<?php if (isset($current->sorted_fields[0])): // non-grouped fields ?>
	<?php foreach ($current->sorted_fields[0] as $field_id => $field): ?>
		<?php echo Layout::render('core.submission.formFields.field', ['k' => $k, 'field' => $field]) // field part ?>
	<?php endforeach; ?>

	<?php unset($current->sorted_fields[0]); ?>
<?php endif; ?>

<?php echo Layout::render('core.submission.formFields.tags', ['current' => $current, 'k' => $k]) // tags field part ?>
