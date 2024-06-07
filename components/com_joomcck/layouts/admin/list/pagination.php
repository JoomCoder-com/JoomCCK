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


            <div class="joomcckLimitBox">
				<?php echo $pagination->getLimitBox() ?>
                <small><?php echo $pagination->getResultsCounter(); ?></small>
            </div>

    </div>
</form>