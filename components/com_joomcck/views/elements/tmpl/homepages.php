<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
JHtml::_('dropdown.init');
?>
<br />
<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="sales-form">
	<div class="controls controls-row">
		<table class="pull-right">
			<tr>
				<td width="20px">
					<div class="alert alert-info" style="margin-bottom: 0px;" rel="tipbottom" data-original-title="<?php echo JText::_('CPOSTAUTHOR')?>"></div>
				</td>
				<td width="20px">
					<div class="alert alert-success" style="margin-bottom: 0px;" rel="tipbottom" data-original-title="<?php echo JText::_('CPOSTSUBSCR')?>"></div>
				</td>
				<td width="20px">
					<div class="alert alert-success" style="margin-bottom: 0px; background-color: #fff; border-color: #cccccc" rel="tipbottom" data-original-title="<?php echo JText::_('CPOSTEVERYONE')?>"></div>
				</td>
			</tr>
		</table>
		
		<input type="text" autocomplete="off" name="filter_search" size="16" id="filter_search" onkeyup="Joomcck.searchuserhome(this)" value=""/>
	</div>
	<div class="clearfix"> </div>
	<style>
		.table tbody tr {
			min-height: 30px;
		}
	</style>
	<table class="table table-hover">
		<thead>
			<tr>
				<th width="1%">#</th>
				<th>Post at</th>
			</tr>
		</thead>
		<tbody>
			<tr class="info user-list">
				<td>1</td>
				<td class="has-context">
					
					<?php $out[] = $this->isme ? JText::_('CPOSTHOMEPAGE') : JText::sprintf('CPOSTHOMEPAGEUSER', CCommunityHelper::getName($this->author, JFactory::getApplication()->input->getInt('section_id')))?>
					<?php if($this->params->get('sections.'.JFactory::getApplication()->input->getInt('section_id').'.title')):?>
						<?php $out[] = '<br /><small> '.$this->params->get('sections.'.JFactory::getApplication()->input->getInt('section_id').'.title').'</small>'; ?>
					<?php endif; ?>
					<div class="btn-group pull-right" style="display:none;">
						<button onclick="parent.choosewheretopost(<?php echo $this->author; ?>, '<?php echo htmlentities(str_replace(array("\n", "\r"), '',implode('', $out)), ENT_QUOTES, 'UTF-8') ?>')" type="button" class="btn btn-small btn-primary">Choose</button>
					</div>
					<?php echo implode('', $out); ?>
				</td>
			</tr>
			<?php foreach ($this->items as $key => $value) {
				$out = array();
				$isme = ($value->id == $this->user->get('id'));
				$params = new JRegistry($value->params);

				$out[] = $isme ? JText::_('CPOSTHOMEPAGE') : JText::sprintf('CPOSTHOMEPAGEUSER', CCommunityHelper::getName($value->id, JFactory::getApplication()->input->getInt('section_id')));
				if($params->get('sections.'.JFactory::getApplication()->input->getInt('section_id').'.title'))
				{
					$out[] = '<br /><small> '.$params->get('sections.'.JFactory::getApplication()->input->getInt('section_id').'.title').'</small>';
				}
				echo sprintf('<tr class="success user-list"><td>%s</td><td class="has-context"><div class="btn-group pull-right" style="display:none;">
						<button onclick="parent.choosewheretopost(%d, \'%s\')" type="button" class="btn btn-small btn-primary">Choose</button>
					</div>%s</td></tr>', $key + 2, $value->id, htmlentities(str_replace(array("\n", "\r"), '',implode('', $out)), ENT_QUOTES, 'UTF-8'), implode('', $out));
			}?>
			<?php foreach ($this->all as $key => $value) {
				$out = array();
				$isme = ($value->id == $this->user->get('id'));
				$params = new JRegistry($value->params);

				$out[] = $isme ? JText::_('CPOSTHOMEPAGE') : JText::sprintf('CPOSTHOMEPAGEUSER', CCommunityHelper::getName($value->id, JFactory::getApplication()->input->getInt('section_id')));
				if($params->get('sections.'.JFactory::getApplication()->input->getInt('section_id').'.title'))
				{
					$out[] = '<br /><small> '.$params->get('sections.'.JFactory::getApplication()->input->getInt('section_id').'.title').'</small>';
				}
				echo sprintf('<tr class=" user-list"><td>%s</td><td class="has-context"><div class="btn-group pull-right" style="display:none;">
						<button onclick="parent.choosewheretopost(%d, \'%s\')" type="button" class="btn btn-small btn-primary">Choose</button>
					</div>%s</td></tr>', $key + 2, $value->id, htmlentities(str_replace(array("\n", "\r"), '',implode('', $out)), ENT_QUOTES, 'UTF-8'), implode('', $out));
			}?>
		</tbody>
	</table>
</form>
<script>
(function($){
	
	var list = $('.user-list');
	var string = [];
	var html = '';

	$('*[rel="tipbottom"]').tooltip({placement:'bottom'});

	$.each(list, function(key, val){
		string[key] = $(val).children('td.has-context').html();
	});

	Joomcck.searchuserhome = function(el)
	{
		var needle = $(el).val();
		var haystack = '';
		var td = null;

		$.each(list, function(key, val){
			td = $(val).children('td.has-context');
			haystack = td.text().toLowerCase();

			if (haystack.indexOf(needle.toLowerCase(), null) !== -1) {
				$(val).css('display', 'table-row');
				
				var regex = new RegExp('(<[^>]*>)|(\\b'+ needle.replace(/([-.*+?^${}()|[\]\/\\])/g,"\\$1") +')', 'ig');
				$.each(td, function(k, v){
					$(v).html(string[key].replace(regex, function(a, b, c, d){
						return (a.charAt(0) == '<') ? a : '<strong class="label label-warning">' + c + '</strong>'; 
					}));
				});
			}
			else
			{
				$(val).css('display', 'none');
			}
		});

	}
})(jQuery);
</script>
