<?php
/**
 * Joomcck by joomcoder
 * Core Layout - Compare Bar
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

extract($displayData);

?>
<div id="compare" <?php echo !$current->compare ? 'class="hide"' : ''; ?>>
	<div class="alert alert-info alert-block">
		<h4><?php echo Text::sprintf('CCOMPAREMSG', $current->compare) ?></h4>
		<br><a rel="nofollow"
			   href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=compare&section_id=' . $current->section->id . '&return=' . Url::back()); ?>"
			   class="btn btn-primary"><?php echo Text::_('CCOMPAREVIEW'); ?></a>
		<button onclick="Joomcck.CleanCompare(null, '<?php echo @$current->section->id ?>')"
				class="btn"><?php echo Text::_('CCLEANCOMPARE'); ?></button>
	</div>
</div>
