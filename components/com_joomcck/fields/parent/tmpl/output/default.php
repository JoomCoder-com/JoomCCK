<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$key = $this->id.'-'.$record->id;

echo $this->content['html'];

if($this->show_btn_new)
{
	$url = 'index.php?option=com_joomcck&view=form';
	$url .= '&section_id='.$section->id;
	$url .= '&type_id='.$type->id;
	$url .= '&fand='.$record->id;
	$url .= '&field_id='.$this->params->get('params.child_field');
	$url .= '&return='.Url::back();
	$url .= '&Itemid='.$section->params->get('general.category_itemid');

	$links[] = sprintf('<a href="%s" class="btn btn-sm btn-light border">%s</a>', JRoute::_($url), JText::_($this->params->get('params.invite_add_more')));
}

if($this->show_btn_exist)
{
	$doTask = JRoute::_('index.php?option=com_joomcck&view=elements&layout=records&tmpl=component&section_id='.$section->id.
		'&type_id='.$type->id.
		'&record_id='.$record->id.
		'&type='.$this->type.
		'&field_id='.$this->id.
		'&excludes='.implode(',', $this->content['ids']), false);

	$links[] = "<a data-toggle=\"modal\" role=\"button\" class=\"btn btn-sm btn-light border\" href=\"#modal_{$key}\">\n".JText::_($this->params->get('params.add_existing_label'))."</a>\n";
	?>
		<div style="width:770px;" class="modal hide fade" id="modal_<?php echo $key;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('FS_ATTACHEXIST');?></h3>
			</div>

			<div class="modal-body" style="overflow-x: hidden; max-height:500px; padding:0;">
				<iframe frameborder="0" width="100%" height="410px"></iframe>
			</div>

			<div class="modal-footer">
				<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
			</div>
		</div>
		<script>
		(function($){
			$('#modal_<?php echo $key;?>').on('show', function(){
				$("iframe", this).attr('src', '<?php echo $doTask;?>');
			});
		}(jQuery));
		</script>
	<?php
}

if($this->show_btn_all)
{
	$links[] = sprintf('<a href="%s" class="btn btn-sm btn-light border">%s</a>', JRoute::_($this->show_btn_all), JText::_($this->params->get('params.invite_view_more')));
}
?>

<?php if(!empty($links)): ?>
	<?php echo implode(' ', $links);?>
<?php endif; ?>