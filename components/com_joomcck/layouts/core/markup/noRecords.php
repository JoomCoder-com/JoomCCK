<?php
/**
 * Joomcck by joomcoder
 * Core Layout - No Records Message
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$markup = $current->tmpl_params['markup'];

?>
<?php if (
	((!empty($current->category->id) && $current->category->params->get('submission')) || (empty($current->category->id) && $current->section->params->get('general.section_home_items'))) && !$current->input->get('view_what') && $markup->get('main.display_no_records_warning',1)): ?>
	<p class="jcck-no-records alert alert-warning" id="no-records<?php echo $current->section->id; ?>">
		<i class="fas fa-exclamation-triangle"></i> <?php echo Text::_('CNOARTICLESHERE'); ?>
	</p>
<?php endif; ?>
