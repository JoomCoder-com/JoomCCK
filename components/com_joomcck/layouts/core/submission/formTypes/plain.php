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

// some inits
$k = 0;

?>
<div id="joomcck-submission-form" class="jcck-form-plain">

	<?php echo Layout::render('core.submission.formParts.mainFields',['current' => $current, 'k' => $k]) ?>

	<?php if (isset($current->sorted_fields)): // grouped fields ?>
		<?php foreach ($current->sorted_fields as $group_id => $fields) : ?>		
			
			<?php if (!empty($current->field_groups[$group_id]['descr'])): ?>
				<?php echo $current->field_groups[$group_id]['descr']; ?>
			<?php endif; ?>
        
			<?php foreach ($fields as $field_id => $field): ?>
				<?php echo Layout::render('core.submission.formFields.field', ['current' => $current,'k' => $k, 'field' => $field]) // field part ?>
			<?php endforeach; ?>
			
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if (count($current->meta)): ?>
		<?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]) // metadata field part ?>
	<?php endif; ?>


	<?php if (count($current->core_admin_fields)): ?>
		<?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]) // admin field part ?>
	<?php endif; ?>

	<?php if (count($current->core_fields)): ?>

		<?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]) // core field part ?>
	<?php endif; ?>

</div>


