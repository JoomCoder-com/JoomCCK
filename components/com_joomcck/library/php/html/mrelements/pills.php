<?php

/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') || die('Restricted access');
?>
<style>

</style>
<div id="bs-tags-<?php echo $id ?>" class="tag-pills"></div>

<script>
(function($){
    $('#bs-tags-<?php echo $id ?>').tags({
        values: <?php echo json_encode($default) ?>,
        <?php if(!empty($options['suggestion_url'])): ?>
            suggestion_url: '<?php echo JRoute::_($options['suggestion_url'], false) ?>',
        <?php endif; ?>
        <?php if(count($list) > 0): ?>
        suggestions: <?php echo json_encode($list) ?>,
        <?php endif; ?>
        limit: <?php echo (int)@$options['limit']; ?>,
        only_suggestions: <?php echo $options['only_suggestions'] ? 'true' : 'false'; ?>,
        suggestion_limit: <?php echo $options['suggestion_limit']; ?>,
        input_name: '<?php echo $name ?>[]',
        can_delete: <?php echo $options['can_delete'] ? 'true' : 'false'; ?>,
        can_add: <?php echo $options['can_add'] ? 'true' : 'false'; ?>,
        <?php if(!empty($options['onAdd'])): ?>
            onBeforeAdd: function(pill, value) {
                $.ajax({
                    dataType: 'json', 
                    type: 'get', async: false, 
                    url: '<?php echo JRoute::_($options['onAdd']) ?>',
                    data: {tid: value.id, text: value.text}
                }).done(function(json) {
                    console.log(json);
                });
                return pill;
            },
        <?php endif; ?>
        <?php if(!empty($options['onRemove'])): ?>
            onRemove: function(pill){
                $.ajax({
                    dataType: 'json', 
                    type: 'get', async: false, 
                    url: '<?php echo JRoute::_($options['onRemove']) ?>',
                    data: {tid: pill.children('input').val()}
                }).done(function(json) {
                    //console.log(1);
                });
            },
        <?php endif; ?>

        templates: {
            list: $(document.createElement('ul')).addClass('pills-list'),
            delete_icon: '<?php echo HTMLFormatHelper::icon('cross.png'); ?>',
            ok_icon: '<button type="button" class="btn btn-link"><?php echo HTMLFormatHelper::icon('tick.png'); ?></button>',
            plus_icon: '<?php echo HTMLFormatHelper::icon('plus.png'); ?>',
            pill: '<li class="active tag-badge">{0}</li>',
            add_pill: ' <button type="button" class=" clearfix btn tag-badge"></button>',
            input_pill: '<div class="clearfix form-inline tag-badge"></div>',
            number: ' <sup><small>{0}</small></sup>'
        }
    });
}(jQuery))
</script>