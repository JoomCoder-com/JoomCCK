<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

?>
<div class="joomcck-multirating list-group mb-3">
    <div class="list-group-item list-group-item-primary d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div class="fw-bold"><?php echo \Joomla\CMS\Language\Text::_('CTOTALRATING'); ?></div>
        <div class="text-md-end">
            <?php echo RatingHelp::loadRating($type->params->get('properties.tmpl_rating'), round(@$record->votes_result), $record->id, 500, 'Joomcck.ItemRatingCallBack', false, $record->id); ?>
            <small id="rating-text-<?php echo $record->id; ?>" class="d-block d-md-inline ms-md-2 text-muted"><?php echo \Joomla\CMS\Language\Text::sprintf('CRAINGDATA', $record->votes_result, $record->votes); ?></small>
        </div>
    </div>

    <?php foreach ($options as $key => $option): ?>
        <?php
        $parts   = explode('::', $option);
        $canRate = self::canRate('record', $record->user_id, $record->id, $type->params->get('properties.rate_access'), $key, $type->params->get('properties.rate_access_author', 0));
        ?>
        <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div class="fw-semibold"><?php echo \Joomla\CMS\Language\Text::_($parts[0]); ?></div>
            <div class="text-md-end">
                <?php echo RatingHelp::loadRating(
                    isset($parts[1]) ? $parts[1] : $type->params->get('properties.tmpl_rating'),
                    round((int) @$result[$key]['sum']),
                    $record->id,
                    $key,
                    'Joomcck.ItemRatingCallBackMulti',
                    $canRate
                ); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
