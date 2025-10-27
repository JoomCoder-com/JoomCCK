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

if (!$current->navigation || (!$current->navigation->next && !$current->navigation->previous)) {
    return;
}

$lang = \Joomla\CMS\Factory::getLanguage();
?>

<div class="joomcck-navigation my-4">
    <div class="row">
        <?php if ($current->navigation->previous): ?>
            <div class="col-md-6 text-start">
                <div class="joomcck-nav-previous">
                    <a href="<?php echo $current->navigation->previous->url; ?>" class="btn btn-outline-primary" title="<?php echo $current->navigation->previous->title ?>">
                        <i class="fa fa-chevron-left" aria-hidden="true"></i>
                        <span class="nav-label"><?php echo \Joomla\CMS\Language\Text::_('CPREVIOUS_RECORD'); ?></span>
                    </a>
                    <div class="nav-title">
                        <small><?php echo \Joomla\CMS\HTML\Helpers\StringHelper::truncate($current->navigation->previous->title, 50) ?></small>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-6"></div>
        <?php endif; ?>

        <?php if ($current->navigation->next): ?>
            <div class="col-md-6 text-end">
                <div class="joomcck-nav-next">
                    <a href="<?php echo $current->navigation->next->url; ?>" class="btn btn-outline-primary" title="<?php echo $current->navigation->next->title ?>">
                        <span class="nav-label"><?php echo \Joomla\CMS\Language\Text::_('CNEXT_RECORD'); ?></span>
                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    </a>
                    <div class="nav-title">
                        <small><?php echo \Joomla\CMS\HTML\Helpers\StringHelper::truncate($current->navigation->next->title, 50) ?></small>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-6"></div>
        <?php endif; ?>
    </div>
</div>