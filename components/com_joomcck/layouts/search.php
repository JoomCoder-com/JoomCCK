<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
$filters = [];
if($displayData->state->get('filter.search')) {
	$filters['filter.search'] = 'filter.search';
}

if(is_array($displayData->_filters)){
	foreach ($displayData->_filters AS $i => $filter) {
		// Exclude type filter in fields llist
		if($displayData->input->get('view') == 'tfields' && $filter['id'] == 'filter_type') {
			continue;
		}
		if($displayData->state->get(str_replace('_', '.', $filter['id']))) {
			$filters[str_replace('_', '.', $filter['id'])] = $filter['id'];
		}
	}
}

?>

<div class="float-end search-box">

    <div class="input-group">
        <input type="text" class="form-control" aria-label="<?php echo JText::_('CSEARCHPLACEHOLDER'); ?>" placeholder="<?php echo JText::_('CSEARCHPLACEHOLDER'); ?>" name="filter_search" id="filter_search" value="<?php echo $displayData->state->get('filter.search'); ?>"/>
	    <?php if(!empty($displayData->_filters)): ?>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" rel="tooltip" data-bs-target="#list-filters-box" data-original-title="<?php echo JText::_('CFILTER'); ?>">
			    <?php echo HTMLFormatHelper::icon('funnel.png'); ?>
            </button>
	    <?php endif; ?>
	    <?php if($filters): ?>
            <button rel="tooltip" class="btn btn-outline-warning" type="button" id="cob-filters-reset" data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
			    <?php echo HTMLFormatHelper::icon('cross.png'); ?>
            </button>
	    <?php endif; ?>
        <button class="btn btn-outline-secondary" rel="tooltip" type="submit" data-original-title="<?php echo JText::_('CSEARCH'); ?>">
		    <?php echo HTMLFormatHelper::icon('magnifier.png'); ?>
        </button>
    </div>
</div>
<script>
(function($){
	$('#cob-filters-reset').click(function(){
		<?php foreach($filters as $filter): ?>
		document.getElementById('<?php echo $filter ?>').value = '';
		<?php endforeach; ?>
		this.form.submit();
	});
}(jQuery))
</script>