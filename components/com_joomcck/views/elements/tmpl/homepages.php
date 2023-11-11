<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
?>
<br />
<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="sales-form">
	<div class="controls controls-row">
		<table class="float-end">
			<tr>
				<td width="20px">
					<div class="alert alert-info" style="margin-bottom: 0px;" rel="tipbottom" data-bs-title="<?php echo \Joomla\CMS\Language\Text::_('CPOSTAUTHOR')?>"></div>
				</td>
				<td width="20px">
					<div class="alert alert-success" style="margin-bottom: 0px;" rel="tipbottom" data-bs-title="<?php echo \Joomla\CMS\Language\Text::_('CPOSTSUBSCR')?>"></div>
				</td>
				<td width="20px">
					<div class="alert alert-success" style="margin-bottom: 0px; background-color: #fff; border-color: #cccccc" rel="tipbottom" data-bs-title="<?php echo \Joomla\CMS\Language\Text::_('CPOSTEVERYONE')?>"></div>
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
					
					<?php $out[] = $this->isme ? \Joomla\CMS\Language\Text::_('CPOSTHOMEPAGE') : \Joomla\CMS\Language\Text::sprintf('CPOSTHOMEPAGEUSER', CCommunityHelper::getName($this->author, \Joomla\CMS\Factory::getApplication()->input->getInt('section_id')))?>
					<?php if($this->params->get('sections.'.\Joomla\CMS\Factory::getApplication()->input->getInt('section_id').'.title')):?>
						<?php $out[] = '<br /><small> '.$this->params->get('sections.'.\Joomla\CMS\Factory::getApplication()->input->getInt('section_id').'.title').'</small>'; ?>
					<?php endif; ?>
					<div class="btn-group float-end" style="display:none;">
						<button onclick="parent.choosewheretopost(<?php echo $this->author; ?>, '<?php echo htmlentities(str_replace(array("\n", "\r"), '',implode('', $out)), ENT_QUOTES, 'UTF-8') ?>')" type="button" class="btn btn-sm btn-primary">Choose</button>
					</div>
					<?php echo implode('', $out); ?>
				</td>
			</tr>
			<?php foreach ($this->items as $key => $value) {
				$out = array();
				$isme = ($value->id == $this->user->get('id'));
				$params = new \Joomla\Registry\Registry($value->params);

				$out[] = $isme ? \Joomla\CMS\Language\Text::_('CPOSTHOMEPAGE') : \Joomla\CMS\Language\Text::sprintf('CPOSTHOMEPAGEUSER', CCommunityHelper::getName($value->id, \Joomla\CMS\Factory::getApplication()->input->getInt('section_id')));
				if($params->get('sections.'.\Joomla\CMS\Factory::getApplication()->input->getInt('section_id').'.title'))
				{
					$out[] = '<br /><small> '.$params->get('sections.'.\Joomla\CMS\Factory::getApplication()->input->getInt('section_id').'.title').'</small>';
				}
				echo sprintf('<tr class="success user-list"><td>%s</td><td class="has-context"><div class="btn-group float-end" style="display:none;">
						<button onclick="parent.choosewheretopost(%d, \'%s\')" type="button" class="btn btn-sm btn-primary">Choose</button>
					</div>%s</td></tr>', $key + 2, $value->id, htmlentities(str_replace(array("\n", "\r"), '',implode('', $out)), ENT_QUOTES, 'UTF-8'), implode('', $out));
			}?>
			<?php foreach ($this->all as $key => $value) {
				$out = array();
				$isme = ($value->id == $this->user->get('id'));
				$params = new \Joomla\Registry\Registry($value->params);

				$out[] = $isme ? \Joomla\CMS\Language\Text::_('CPOSTHOMEPAGE') : \Joomla\CMS\Language\Text::sprintf('CPOSTHOMEPAGEUSER', CCommunityHelper::getName($value->id, \Joomla\CMS\Factory::getApplication()->input->getInt('section_id')));
				if($params->get('sections.'.\Joomla\CMS\Factory::getApplication()->input->getInt('section_id').'.title'))
				{
					$out[] = '<br /><small> '.$params->get('sections.'.\Joomla\CMS\Factory::getApplication()->input->getInt('section_id').'.title').'</small>';
				}
				echo sprintf('<tr class=" user-list"><td>%s</td><td class="has-context"><div class="btn-group float-end" style="display:none;">
						<button onclick="parent.choosewheretopost(%d, \'%s\')" type="button" class="btn btn-sm btn-primary">Choose</button>
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
