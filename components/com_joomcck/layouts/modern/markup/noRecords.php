<?php
/**
 * Joomcck by joomcoder
 * Modern UI - No Records Message Layout
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$markup = $current->tmpl_params['markup'];

?>
<?php if (
	((!empty($current->category->id) && $current->category->params->get('submission')) || (empty($current->category->id) && $current->section->params->get('general.section_home_items'))) && !$current->input->get('view_what') && $markup->get('main.display_no_records_warning',1)): ?>
	<div class="jcck-no-records jcck-alert flex items-center gap-2 px-4 py-3 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-sm" id="no-records<?php echo $current->section->id; ?>">
		<i class="fas fa-exclamation-triangle"></i> <?php echo Text::_('CNOARTICLESHERE'); ?>
	</div>
<?php endif; ?>
