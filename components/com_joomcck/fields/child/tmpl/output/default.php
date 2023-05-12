<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();
$key = $this->id.'-'.$record->id;

echo $this->content['html'];

if($this->show_btn_new && isset($type->id))
{
	$url = 'index.php?option=com_joomcck&view=form';
	$url .= '&section_id='.$section->id;
	$url .= '&type_id='.$type->id;
	$url .= '&fand='.$record->id;
	$url .= '&field_id='.$this->params->get('params.parent_field');
	$url .= '&return='.Url::back();
	$url .= '&Itemid='.$section->params->get('general.category_itemid');

	$links[] = sprintf('<a href="%s" class="btn btn-sm btn-light border">%s</a>', JRoute::_($url), JText::_($this->params->get('params.invite_add_more')));
}

if($this->show_btn_exist && isset($type->id))
{
	$doTask = JURI::root(TRUE).'/index.php?option=com_joomcck&view=elements&layout=records&tmpl=component&section_id='.$section->id.
	'&type_id='.$type->id.
	'&record_id='.$record->id.
	'&type='.$this->type.
	'&field_id='.$this->id.
	'&excludes='.implode(',', $this->content['ids']);

	HTMLHelper::_('bootstrap.modal');

	$links[] = "<a data-bs-toggle=\"modal\" role=\"button\" class=\"btn btn-sm btn-light border\" href=\"#modal_".$key."\">\n".JText::_($this->params->get('params.add_existing_label'))."</a>\n";
	?>


    <div class="modal fade" id="modal_<?php echo $key;?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="exampleModalLabel"><?php echo JText::_('FS_ATTACHEXIST');?></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe src="<?php echo $doTask ?>" frameborder="0" width="100%" height="410px"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


	<?php
}

if($this->show_btn_all)
{
	$links[] = sprintf('<a href="%s" class="btn btn-sm btn-light border">%s</a>', JRoute::_($this->show_btn_all), $this->params->get('params.invite_view_more'));
}
?>

<?php if(!empty($links)): ?>
	<?php echo implode(' ', $links);?>
<?php endif; ?>