<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\Component\Finder\Administrator\Indexer\Parser\Html;

defined('_JEXEC') or die();

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');
JHTML::_('bootstrap.tooltip', '*[rel="tooltipright"]',
	array(
		'placement' => 'right'
	)
);


$id = $record->id.$this->id;
$list = array();
$params = $this->params;
$path = JURI::root().'components/com_joomcck/fields/status/icons/';
$access = array(4=>3,5=>3,6=>0,1=>1,2=>1,3=>1);
foreach($this->statuses as $key => $status)
{
	$icon = JHtml::image($path . $params->get('params.icon' . $key), $status, array('align' => 'absmiddle'));
	if (!empty($this->color[$key]))
	{
		$status = '<span style="color: ' . $this->color[$key] . '">' . $status . '</span>';
	}
	$li_html = $icon . ' ' . $status;
	if($this->checkStatus($params->get('params.access' . $key, $access[$key]), 'edit'))
	{
		$list[] = sprintf('<li rel="tooltipright" title="%s"><button type="button" class="dropdown-item" onclick="changeStatus%d(%d, \'%s\')">%s</button></li>',
			htmlentities(JText::sprintf('ST_CLICKTOCHANGE', $status), ENT_QUOTES, 'UTF-8'), $id, $key, $this->clienttype, $li_html);
	}
}
?>

<span id="field_<?php echo $id;?>"><?php echo $this->out;?></span>

<?php if (count($list)): ?>
	<div class="dropdown d-inline">
		<button id="field_<?php echo $id;?>_button" type="button" aria-expanded="false" class="dropdown-toggle btn btn-sm btn-outline-dark" data-bs-toggle="dropdown" rel="tooltip" title="<?php echo JText::_('ST_CHANGETO');?>"></button>
		<ul class="dropdown-menu" ><?php echo implode("\n", $list);?></ul>
	</div>
<?php endif; ?>
<div class="clearfix"></div>
<script>

function changeStatus<?php echo $id;?>(to, type)
{
	if(!to) return;
	
	jQuery.ajax({
		url: Joomcck.field_call_url,
		type:'post',
		dataType: 'json',
		data:{
			field_id: <?php echo $this->id;?>,
			func: "_changeStatus",
			record_id: <?php echo $record->id;?>, 
			section_id: <?php echo $section->id;?>,
			to: to, 
			ajax: 1,
			view_type: '<?php echo $this->clienttype;?>'
		}
	}).done(function(json) {
		if(!json.success)
		{
			alert(json.error);
			return;
		}
		jQuery("#field_<?php echo $id;?>").html(json.result);
	});
}
</script>