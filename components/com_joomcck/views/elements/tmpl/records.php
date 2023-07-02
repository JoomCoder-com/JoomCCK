<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$fid = JFactory::getApplication()->input->getInt('field_id');
$k = 0;
?>
<style>
	.list-item {
		margin-bottom: 5px;
	}
	#recordslist {
		margin-top: 20px;
	}
</style>

<script type="text/javascript">
(function ($) {
	window.closeWindow = function()
	{
		list = $('#recordslist').children('div.alert');
		parent['updatelist<?php echo $fid?>'](list);
		parent['modal<?php echo $fid; ?>'].modal('hide');
	}
	window.attachRecord = function(el)
	{
		var id = el.attr('rel');
		var title = el.children('span').text();
		<?php if(JFactory::getApplication()->input->get('mode') == 'form'):?>
			var multi = parent['multi<?php echo $fid; ?>'];
			var limit = parent['limit<?php echo $fid; ?>'];
			var inputname = parent['name<?php echo $fid; ?>'];

			list = $('#recordslist');
			if(!multi)
			{
				list.html('');
			}
			else
			{
				lis = list.children('div.alert');
				if(lis.length >= limit) {
					alert('<?php echo JText::_("CERRJSMOREOPTIONS");?>');
					return false;
				}
				error = 0;
				$.each(lis, function(k, v){
					if($(v).attr('rel') == id){
						alert('<?php echo JText::_('CALREADYSELECTED');?>');
						error = 1;
					}
				});
				if(error) return false;
			}
			var el = $(document.createElement('div'))
				.attr({
					'class': 'alert alert-info list-item',
					rel: id
				})
				.html('<a class="close" data-dismiss="alert" href="#">x</a><span>'+title+'</span><input type="hidden" name="'+inputname+'" value="'+id+'">')
				.appendTo(list);
		<?php else: ?>
			$.ajax({
				url: Joomcck.field_call_url,
				dataType: 'json',
				type: 'POST',
				data:{
					field_id: <?php echo JFactory::getApplication()->input->getInt('field_id');?>,
					func:'onAttachExisting',
					field:'<?php echo JFactory::getApplication()->input->get('type');?>',
					record_id:<?php echo JFactory::getApplication()->input->getInt('record_id');?>,
					attach:id
				}
			}).done(function(json) {
				if(!json.success)
				{
					alert(json.error);
					return;
				}
				parent.location.reload();
				parent['modal<?php echo $fid; ?>'].modal('hide');
			});
		<?php endif;?>
	}
}(jQuery));
</script>

<form name="adminForm" id="adminForm" method="post">
    <div class="input-group">
        <input type="text" class="form-control form-control-sm" name="filter_search2" id="filter_search2" value="<?php echo $this->state->get('records.search2'); ?>" />
        <button class="btn btn-sm btn-outline-success" type="submit">
            <span class="fas fa-search"></span> <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
        </button>
        <button class="btn btn-sm btn-outline-danger btn-sm" type="button" onclick="document.getElementById('filter_search2').value='';this.form.submit();">
            <span class="fas fa-eraser"></span> <?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
        </button>
		<?php if(JFactory::getApplication()->input->get('mode') == 'form'):?>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="closeWindow()">
                <span class="fas fa-check"></span>
				<?php echo JText::_('CAPPLY');?></button>
		<?php endif;?>
    </div>
	<div class="clearfix"></div>

	<div class="container-fluid">
	<?php if(JFactory::getApplication()->input->get('mode') == 'form'):?>
		<div class="row">
			<div class="col-md-8">

	<?php endif;?>

		<table class="table">
			<thead>
				<th width="1%">
					<?php echo JText::_('CNUM'); ?>
				</th>
				<th>
					<?php echo JText::_('CTITLE')?>
				</th>
			</thead>
			<tbody>
				<?php foreach ($this->items AS $i => $item):?>
					<tr class="cat-list-row<?php echo $k = 1 - $k; ?>">
						<td><?php echo $this->pagination->getRowOffset($i); ?></td>
						<td><a href="javascript:void(0)" rel="<?php echo $item->id?>"><span><?php echo $item->title?></span></a></td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		<div class="float-end"><?php echo $this->pagination->getPagesCounter(); ?></div>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<script type="text/javascript">
			(function($){
				$('a[rel]').on('click', function(){
					attachRecord($(this));
				});
			}(jQuery))
		</script>

	<?php if(JFactory::getApplication()->input->get('mode') == 'form'):?>
			</div>
			<div class="col-md-4">
				<div id="recordslist">

				</div>
			</div>
		</div>
		<script type="text/javascript">
			(function($){
				var listofselected = $(parent['elementslist<?php echo JFactory::getApplication()->input->getInt('field_id')?>'])
				.children('div.alert')
				.each(function(){
					attachRecord($(this));
				});
			}(jQuery))
		</script>
	<?php endif;?>
	</div>


	<input type="hidden" name="option" value="com_joomcck" />
	<input type="hidden" name="section_id" value="<?php echo JFactory::getApplication()->input->getInt('section_id')?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="limitstart" value="0" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>