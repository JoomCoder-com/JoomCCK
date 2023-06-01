<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

extract($displayData);

// params
$showPagination = $params->get('tmpl_core.item_pagination', 1);
$showLimitBox = $params->get('tmpl_core.item_limit_box', 0);

// no need to continue if pagination disabled
if(!$showPagination)
    return;


// remove "all" items option, + small select input
if($showLimitBox){
    $limitBox = str_replace('<option value="0">' . JText::_('JALL') . '</option>', '', $pagination->getLimitBox());
	$limitBox = str_replace('class="form-select"', 'class="form-select form-select-sm"', $pagination->getLimitBox());

}

?>

<form method="post">
    <div class="d-flex justify-content-between align-items-center">

		<?php if ($pagination->getPagesLinks()): ?>
            <div class="joomcckPageLinks">
				<?php echo $pagination->getPagesLinks() ?>
            </div>
		<?php endif; ?>

		<?php if ($pagination->getPagesCounter()): ?>
            <div class="joomcckPagesCounter">
				<?php echo $pagination->getPagesCounter(); ?>
            </div>
		<?php endif; ?>

		<?php if ($showLimitBox) : ?>
            <div class="joomcckLimitBox">
				<?php echo $limitBox ?>
                <small><?php echo $pagination->getResultsCounter(); ?></small>
            </div>
		<?php endif; ?>
    </div>
</form>